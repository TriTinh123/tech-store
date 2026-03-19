"""
ai_risk_engine.py — Isolation Forest–based Login Anomaly Detection Engine
==========================================================================

This module is the self-contained AI core for the Adaptive 3FA system.
It wraps sklearn's IsolationForest with:

  • Feature engineering + normalisation metadata
  • Per-feature contribution scoring (which feature drove the anomaly)
  • Structured RiskResult return type (dataclass)
  • Adaptive risk thresholds with four levels: low / medium / high / critical
  • Model persistence (joblib) with auto-train-on-first-use
  • Training data synthesis (normal + anomaly patterns)
  • Evaluation metrics (detection rate, FAR, AUC-ROC)
  • Clean import API for app.py / external callers

Quick usage
-----------
    engine = AnomalyDetector.get_instance()        # singleton, loads model.pkl
    result = engine.score_session({
        "hour_of_day": 3, "is_new_ip": 1,
        "keystroke_speed_ms": 15, ...
    })
    print(result.risk_level, result.requires_3fa, result.explanation)

Training
--------
    python -c "from ai_risk_engine import AnomalyDetector; AnomalyDetector().train_and_save()"
"""

from __future__ import annotations

import logging
import os
import time
from dataclasses import dataclass, field
from typing import Dict, List, Optional, Tuple

import joblib
import numpy as np
from sklearn.ensemble import IsolationForest
from sklearn.neighbors import LocalOutlierFactor
from sklearn.preprocessing import MinMaxScaler

logger = logging.getLogger(__name__)

# ══════════════════════════════════════════════════════════════════════════════
# Constants / configuration
# ══════════════════════════════════════════════════════════════════════════════

MODEL_PATH = os.path.join(os.path.dirname(__file__), "model.pkl")
ENGINE_PATH = os.path.join(os.path.dirname(__file__), "engine.pkl")

# ── Risk thresholds (Isolation Forest decision_function score) ─────────────
# decision_function: higher = more normal, lower = more anomalous
THRESHOLD_CRITICAL = -0.22   # score < this  → CRITICAL
THRESHOLD_HIGH     =  0.08   # score < this  → HIGH  (3FA triggered)
THRESHOLD_MEDIUM   =  0.20   # score < this  → MEDIUM
# score >= 0.20              → LOW

# ── 3FA is triggered at HIGH or above ─────────────────────────────────────
FA3_LEVELS = {"high", "critical"}


# ══════════════════════════════════════════════════════════════════════════════
# Feature specification
# ══════════════════════════════════════════════════════════════════════════════

@dataclass(frozen=True)
class FeatureSpec:
    """Metadata for one input feature."""
    name: str
    default: float          # value used when field is absent from payload
    low: float              # plausible minimum (for normalisation)
    high: float             # plausible maximum (for normalisation)
    description: str

    def clip(self, value: float) -> float:
        return float(np.clip(value, self.low, self.high))

    def normalise(self, value: float) -> float:
        """Map raw value to [0, 1] based on [low, high]."""
        span = self.high - self.low
        return (self.clip(value) - self.low) / span if span > 0 else 0.0


# Ordered list — order MUST be preserved throughout training + inference
FEATURES: List[FeatureSpec] = [
    FeatureSpec("hour_of_day",           12.0,  0.0,   23.0,  "Hour of login (0-23)"),
    FeatureSpec("is_weekend",             0.0,  0.0,    1.0,  "1 if login on sat/sun"),
    FeatureSpec("is_new_ip",              0.0,  0.0,    1.0,  "1 if IP never seen for this account"),
    FeatureSpec("is_new_device",          0.0,  0.0,    1.0,  "1 if device fingerprint is new"),
    FeatureSpec("failed_attempts",        0.0,  0.0,   20.0,  "Recent failed login count (last hour)"),
    FeatureSpec("keystroke_speed_ms",   150.0,  0.0, 2000.0,  "Avg ms between keystrokes"),
    FeatureSpec("keystroke_irregularity", 30.0, 0.0,  500.0,  "Std-dev of keystroke intervals"),
    FeatureSpec("transaction_amount",     0.0,  0.0, 20000.0, "Cart value in 1 000 VND units"),
    FeatureSpec("click_count_per_min",   30.0,  0.0,  600.0,  "Mouse/keyboard clicks per minute on login page"),
    FeatureSpec("session_velocity",       2.0,  0.0,   50.0,  "Login attempts (all devices) in last 24 hours"),
    FeatureSpec("ua_risk_score",          0.0,  0.0,    1.0,  "User-agent risk score: 0=normal browser, 1=bot/empty/suspicious"),
]

FEATURE_NAMES: List[str] = [f.name for f in FEATURES]


# ══════════════════════════════════════════════════════════════════════════════
# RiskResult — structured return type
# ══════════════════════════════════════════════════════════════════════════════

@dataclass
class RiskResult:
    """
    Full risk assessment for one login session.

    Attributes
    ----------
    risk_score : float
        Raw Isolation Forest decision_function score.
        Positive = normal region, negative = anomaly region.
    risk_numeric : int
        0-100 danger scale (100 = maximum risk).
    risk_level : str
        'low' | 'medium' | 'high' | 'critical'
    is_anomaly : bool
        True when IsolationForest.predict() returns -1.
    requires_3fa : bool
        True when risk_level is 'high' or 'critical'.
    explanation : List[str]
        Human-readable reasons for the elevated risk score.
    recommendation : str
        One-line action advice for the calling system.
    feature_contributions : Dict[str, float]
        Per-feature deviation from the training mean (0-1, higher = more suspicious).
    latency_ms : float
        Time to score this session in milliseconds.
    """
    risk_score: float
    risk_numeric: int
    risk_level: str
    is_anomaly: bool
    requires_3fa: bool
    explanation: List[str]
    recommendation: str
    feature_contributions: Dict[str, float] = field(default_factory=dict)
    latency_ms: float = 0.0

    def to_dict(self) -> dict:
        return {
            "risk_score":            self.risk_score,
            "risk_numeric":          self.risk_numeric,
            "risk_level":            self.risk_level,
            "is_anomaly":            self.is_anomaly,
            "requires_3fa":          self.requires_3fa,
            "explanation":           self.explanation,
            "recommendation":        self.recommendation,
            "feature_contributions": self.feature_contributions,
            "latency_ms":            round(self.latency_ms, 2),
        }


# ══════════════════════════════════════════════════════════════════════════════
# AnomalyDetector — main engine class
# ══════════════════════════════════════════════════════════════════════════════

class AnomalyDetector:
    """
    Isolation Forest–based login anomaly detection engine.

    The model is trained on *normal* login sessions only (unsupervised).
    Observations that fall far from the normal training distribution are
    assigned negative scores and classified as anomalies.

    Parameters
    ----------
    n_estimators : int
        Number of isolation trees (default 200).
    contamination : float
        Expected fraction of anomalies in production traffic (default 0.05).
    random_state : int
        Seed for reproducibility.
    """

    # Class-level singleton for get_instance()
    _instance: Optional["AnomalyDetector"] = None

    def __init__(
        self,
        n_estimators: int = 400,
        contamination: float = 0.06,
        random_state: int = 42,
    ) -> None:
        self.n_estimators  = n_estimators
        self.contamination = contamination
        self.random_state  = random_state

        self._model: Optional[IsolationForest] = None
        self._lof:   Optional[LocalOutlierFactor] = None
        # Training-set feature means + stds for contribution scoring
        self._train_mean: Optional[np.ndarray] = None
        self._train_std:  Optional[np.ndarray] = None

    # ── Singleton accessor ────────────────────────────────────────────────────

    @classmethod
    def get_instance(cls) -> "AnomalyDetector":
        """Return a module-level singleton, loading or training as needed."""
        if cls._instance is None:
            engine = cls()
            engine.load()
            cls._instance = engine
        return cls._instance

    @classmethod
    def reset_instance(cls) -> None:
        """Force the singleton to be recreated on next get_instance() call."""
        cls._instance = None

    # ── Model lifecycle ───────────────────────────────────────────────────────

    def train(self, X: np.ndarray) -> "AnomalyDetector":
        """
        Fit the Isolation Forest on a (n_samples, n_features) array of
        *normal* sessions.  Stores per-feature mean/std for contribution scoring.

        Returns self for chaining.
        """
        if X.shape[1] != len(FEATURES):
            raise ValueError(
                f"Expected {len(FEATURES)} features, got {X.shape[1]}"
            )

        self._model = IsolationForest(
            n_estimators=self.n_estimators,
            max_samples="auto",
            contamination=self.contamination,
            random_state=self.random_state,
            n_jobs=-1,
        )
        self._model.fit(X)

        # Local Outlier Factor as second-opinion ensemble member (novelty=True for predict)
        self._lof = LocalOutlierFactor(
            n_neighbors=min(20, max(5, X.shape[0] // 100)),
            contamination=self.contamination,
            novelty=True,
            n_jobs=-1,
        )
        self._lof.fit(X)

        # Record training distribution for contribution scoring
        self._train_mean = X.mean(axis=0)
        self._train_std  = X.std(axis=0) + 1e-9   # avoid div-by-zero

        logger.info(
            "IsolationForest (%d trees) + LOF trained on %d samples.",
            self.n_estimators, X.shape[0],
        )
        return self

    def save(self, path: str = ENGINE_PATH) -> None:
        """Persist the fitted engine (model + stats) to disk."""
        if self._model is None:
            raise RuntimeError("Engine has not been trained yet.")
        joblib.dump(self, path)
        logger.info("Engine saved to %s", path)

    def load(self, path: str = ENGINE_PATH) -> "AnomalyDetector":
        """
        Load a previously saved engine from *path*.
        If the engine file doesn't exist, falls back to the legacy model.pkl
        (sklearn model only), then trains fresh if neither exists.
        """
        if os.path.exists(path):
            loaded: AnomalyDetector = joblib.load(path)
            self._model      = loaded._model
            self._lof        = getattr(loaded, '_lof', None)   # back-compat
            self._train_mean = loaded._train_mean
            self._train_std  = loaded._train_std
            logger.info("Engine loaded from %s", path)
            return self

        # Legacy fallback: plain sklearn model saved by model.py / train.py
        if os.path.exists(MODEL_PATH):
            logger.info("engine.pkl not found — loading legacy model.pkl")
            self._model = joblib.load(MODEL_PATH)
            # Approximate training stats from synthetic data
            X_normal, _ = generate_training_data()
            self._train_mean = X_normal.mean(axis=0)
            self._train_std  = X_normal.std(axis=0) + 1e-9
            return self

        logger.warning("No saved model found — training from scratch.")
        self.train_and_save()
        return self

    def train_and_save(self, path: str = ENGINE_PATH) -> "AnomalyDetector":
        """
        Generate synthetic data, train, evaluate, and persist in one step.
        This is the recommended way to initialise the engine.
        """
        X_normal, X_anomaly = generate_training_data()
        self.train(X_normal)
        metrics = self.evaluate(X_normal, X_anomaly)
        _print_training_report(metrics)
        self.save(path)
        # Also save legacy model.pkl so train.py / model.py still work
        joblib.dump(self._model, MODEL_PATH)
        logger.info("Legacy model.pkl updated.")
        return self

    # ── Scoring ───────────────────────────────────────────────────────────────

    def score_session(self, payload: dict) -> RiskResult:
        """
        Analyse one login session payload dict and return a RiskResult.

        The payload must contain the keys defined in FEATURE_NAMES.
        Unknown keys are silently ignored; missing keys use feature defaults.
        """
        if self._model is None:
            raise RuntimeError("Engine is not fitted. Call load() or train_and_save() first.")

        t0 = time.perf_counter()

        X = _build_feature_vector(payload)

        raw_score  = float(self._model.decision_function(X)[0])
        prediction = int(self._model.predict(X)[0])   # 1=normal, -1=anomaly

        # ── LOF ensemble: second opinion ──────────────────────────────────────
        lof_anomaly = False
        if self._lof is not None:
            try:
                lof_score   = float(self._lof.decision_function(X)[0])
                lof_anomaly = int(self._lof.predict(X)[0]) == -1
                # Consensus boost: both models agree → amplify anomaly signal
                if prediction == -1 and lof_anomaly:
                    raw_score -= 0.04   # push score deeper into anomaly region
                # Partial signal: LOF sees anomaly that IF missed → slight penalty
                elif prediction == 1 and lof_anomaly:
                    raw_score -= 0.02
            except Exception:
                pass  # LOF unavailable — use IF score alone

        risk_level   = _score_to_risk(raw_score)
        risk_numeric = _score_to_numeric(raw_score)
        is_anomaly   = prediction == -1 or (raw_score < THRESHOLD_HIGH and lof_anomaly)
        requires_3fa = risk_level in FA3_LEVELS
        contribs     = self._feature_contributions(X[0])
        explanation  = _build_explanation(payload, raw_score, contribs)
        recommendation = _recommendation(risk_level)

        latency_ms = (time.perf_counter() - t0) * 1000

        result = RiskResult(
            risk_score=round(raw_score, 4),
            risk_numeric=risk_numeric,
            risk_level=risk_level,
            is_anomaly=is_anomaly,
            requires_3fa=requires_3fa,
            explanation=explanation,
            recommendation=recommendation,
            feature_contributions=contribs,
            latency_ms=latency_ms,
        )

        logger.info(
            "score=%.4f level=%s 3fa=%s latency=%.1fms reasons=%s",
            raw_score, risk_level, requires_3fa, latency_ms, explanation,
        )
        return result

    # ── Feature contributions ─────────────────────────────────────────────────

    def _feature_contributions(self, x_row: np.ndarray) -> Dict[str, float]:
        """
        Compute a 0-1 suspicion score for each feature.

        Method: z-score of sample value against training distribution,
        then sigmoid-squash to [0, 1].  A higher value means the feature
        is more deviated from the normal training distribution.
        """
        if self._train_mean is None or self._train_std is None:
            return {}

        z = np.abs((x_row - self._train_mean) / self._train_std)
        # sigmoid: 1 / (1 + exp(-z))  maps z≈0 → 0.5, so offset by -0.5 and clip
        suspicion = 1.0 / (1.0 + np.exp(-z)) - 0.5
        suspicion = np.clip(suspicion * 2.0, 0.0, 1.0)  # rescale to [0,1]

        return {
            feat.name: round(float(s), 4)
            for feat, s in zip(FEATURES, suspicion)
        }

    # ── Evaluation ────────────────────────────────────────────────────────────

    def evaluate(
        self,
        X_normal: np.ndarray,
        X_anomaly: np.ndarray,
    ) -> dict:
        """
        Compute detection/false-alarm metrics.

        Returns a dict with:
          - detection_rate   (true-positive rate on anomaly set)
          - false_alarm_rate (false-positive rate on normal set)
          - normal_score_mean / std
          - anomaly_score_mean / std
          - threshold_used
        """
        if self._model is None:
            raise RuntimeError("Engine is not fitted.")

        scores_n = self._model.decision_function(X_normal)
        scores_a = self._model.decision_function(X_anomaly)

        # Using IF's own threshold (contamination parameter)
        tp = int((scores_a < 0.0).sum())
        fp = int((scores_n < 0.0).sum())

        return {
            "n_normal":          len(X_normal),
            "n_anomaly":         len(X_anomaly),
            "detection_rate":    round(tp / len(X_anomaly), 4),
            "false_alarm_rate":  round(fp / len(X_normal), 4),
            "normal_score_mean": round(float(scores_n.mean()), 4),
            "normal_score_std":  round(float(scores_n.std()), 4),
            "anomaly_score_mean": round(float(scores_a.mean()), 4),
            "anomaly_score_std": round(float(scores_a.std()), 4),
            "threshold_used":    0.0,
        }

    # ── Introspection ─────────────────────────────────────────────────────────

    @property
    def is_fitted(self) -> bool:
        return self._model is not None

    def feature_importances(self) -> Dict[str, float]:
        """
        Approximate feature importance based on average path length reduction.
        Uses the mean depth across all trees for each feature as a proxy.
        Returns a dict {feature_name: importance_score} sorted descending.
        """
        if self._model is None:
            raise RuntimeError("Engine is not fitted.")

        # sklearn IsolationForest does not expose feature importances directly,
        # so we proxy via decision-function sensitivity (finite differences)
        baseline = float(self._model.decision_function(
            np.array([[f.default for f in FEATURES]])
        )[0])

        importances: Dict[str, float] = {}
        for i, feat in enumerate(FEATURES):
            perturbed = np.array([[f.default for f in FEATURES]], dtype=float)
            # Perturb this feature by ±10 % of its range
            delta = (feat.high - feat.low) * 0.1
            perturbed[0, i] = feat.default + delta
            score_high = float(self._model.decision_function(perturbed)[0])
            perturbed[0, i] = feat.default - delta
            score_low  = float(self._model.decision_function(perturbed)[0])
            importances[feat.name] = round(abs(score_high - score_low) / (2 * delta + 1e-9), 6)

        return dict(sorted(importances.items(), key=lambda kv: kv[1], reverse=True))

    def __repr__(self) -> str:
        status = "fitted" if self.is_fitted else "untrained"
        return (
            f"AnomalyDetector({status}, "
            f"n_estimators={self.n_estimators}, "
            f"contamination={self.contamination})"
        )


# ══════════════════════════════════════════════════════════════════════════════
# Training data synthesis
# ══════════════════════════════════════════════════════════════════════════════

def generate_training_data(
    n_normal: int = 5000,
    n_anomaly: int = 400,
    seed: int = 42,
) -> Tuple[np.ndarray, np.ndarray]:
    """
    Synthesise labelled login-session data.

    Returns (X_normal, X_anomaly) where each row follows the FEATURES order.
    Only X_normal is used for IsolationForest training (unsupervised).
    X_anomaly is kept for post-training evaluation.
    """
    return (
        _gen_normal(n_normal, seed),
        _gen_anomaly(n_anomaly, seed + 57),
    )


def _gen_normal(n: int, seed: int) -> np.ndarray:
    rng = np.random.default_rng(seed)
    rows = []
    for _ in range(n):
        hour              = int(rng.integers(6, 23))           # typical usage hours
        is_weekend        = int(rng.random() < 0.30)
        is_new_ip         = int(rng.random() < 0.25)           # realistic: ~25% logins from new IP
        is_new_device     = int(rng.random() < 0.15)           # realistic: ~15% logins from new device
        failed_attempts   = int(rng.integers(0, 3))            # 0-2 failed attempts is normal
        ks_speed          = float(np.clip(rng.normal(160, 60), 50, 700))   # human ~50-700ms
        ks_irr            = float(np.clip(rng.normal(40, 20), 5, 250))
        tx_amount         = float(np.clip(rng.normal(500, 400), 0, 5000))
        click_per_min     = float(np.clip(rng.normal(28, 15), 2, 120))   # human: 2-120 clicks/min
        session_velocity  = float(np.clip(rng.exponential(2.5), 1, 10))  # normal: 1-10 sessions/day
        ua_risk           = 0.0 if rng.random() > 0.03 else float(rng.uniform(0.05, 0.2))  # 97% normal UA
        rows.append([hour, is_weekend, is_new_ip, is_new_device,
                     failed_attempts, ks_speed, ks_irr, tx_amount, click_per_min,
                     session_velocity, ua_risk])
    return np.array(rows, dtype=float)


def _gen_anomaly(n: int, seed: int) -> np.ndarray:
    """Eleven anomalous patterns, evenly distributed."""
    rng = np.random.default_rng(seed)
    rows = []
    patterns = [
        "bot", "stuffing", "brute_force_local", "big_tx", "time_travel",
        "account_takeover", "session_hijack", "insider_threat",
        "midnight_fraud", "mobile_bot", "vpn_attacker",
    ]
    per_pattern = n // len(patterns)
    remainder   = n % len(patterns)

    for idx, pattern in enumerate(patterns):
        count = per_pattern + (1 if idx < remainder else 0)
        for _ in range(count):
            if pattern == "bot":
                # Scripted bot: off-hours, new IP/device, very fast + regular typing
                row = [
                    int(rng.integers(0, 5)),      # hour: 0-4 AM
                    0,                             # weekday
                    1, 1,                          # new IP + device
                    int(rng.integers(5, 12)),      # many failed attempts
                    float(np.clip(rng.normal(15, 4), 5, 35)),      # very fast keystrokes
                    float(np.clip(rng.normal(1.2, 0.4), 0.3, 3)),  # near-perfect regularity
                    0.0,                           # no cart (probing only)
                    float(np.clip(rng.normal(400, 60), 280, 590)),  # bot clicks/min
                    float(np.clip(rng.normal(45, 8), 30, 50)),      # high velocity
                    float(rng.uniform(0.85, 1.0)),                  # suspicious UA
                ]
            elif pattern == "stuffing":
                # Credential stuffing: mass failures, new IPs, scripted
                row = [
                    int(rng.integers(1, 5)),
                    0, 1, 1,
                    int(rng.integers(9, 18)),      # very many failures
                    float(np.clip(rng.normal(25, 7), 8, 45)),
                    float(np.clip(rng.normal(3, 1.2), 0.5, 7)),
                    0.0,
                    float(np.clip(rng.normal(440, 80), 320, 590)),
                    float(np.clip(rng.normal(48, 2), 45, 50)),     # near-max velocity
                    float(rng.uniform(0.7, 1.0)),
                ]
            elif pattern == "brute_force_local":
                # Local brute force: 3-7 failed attempts from SAME IP/device (realistic)
                # This captures password guessing or weak credentials being tested
                row = [
                    int(rng.integers(0, 24)),      # any time of day
                    int(rng.random() < 0.2),
                    0, 0,                          # SAME IP and device (local attacker or legit user)
                    int(rng.integers(3, 8)),       # 3-7 failures (realistic range)
                    float(np.clip(rng.normal(180, 60), 70, 400)),  # variable speed
                    float(np.clip(rng.normal(40, 20), 5, 100)),    # variable regularity
                    0.0,                           # no purchase attempt
                    float(np.clip(rng.normal(35, 15), 10, 80)),    # moderate click rate
                    float(np.clip(rng.normal(2, 1.5), 0, 8)),      # some velocity
                    float(rng.uniform(0.0, 0.3)),  # mostly normal browsers
                ]
                # Unusual large purchase from new location, odd hour
                row = [
                    int(rng.integers(2, 6)),
                    0, 1, 1,
                    0,
                    float(np.clip(rng.normal(80, 20), 40, 150)),
                    float(np.clip(rng.normal(12, 4), 3, 25)),
                    float(np.clip(rng.normal(7000, 1500), 4500, 14000)),  # very large tx
                    float(np.clip(rng.normal(42, 14), 10, 90)),
                    float(np.clip(rng.normal(3, 1), 1, 7)),
                    float(rng.uniform(0.0, 0.15)),   # real browser
                ]
            elif pattern == "time_travel":
                # Legitimate-looking but suspicious timing + new location
                row = [
                    int(rng.integers(2, 5)),
                    0, 1, 0,
                    int(rng.integers(2, 6)),
                    float(np.clip(rng.normal(100, 30), 40, 200)),
                    float(np.clip(rng.normal(20, 8), 5, 50)),
                    float(np.clip(rng.normal(2800, 700), 1200, 5500)),
                    float(np.clip(rng.normal(28, 10), 5, 70)),
                    float(np.clip(rng.normal(4, 2), 1, 10)),
                    float(rng.uniform(0.0, 0.1)),
                ]
            elif pattern == "account_takeover":
                # Stolen credentials — human-like speed but wrong device/IP, odd hour
                row = [
                    int(rng.integers(1, 4)),       # early morning
                    0,
                    1, 1,                          # new IP and device (attacker's machine)
                    int(rng.integers(3, 8)),
                    float(np.clip(rng.normal(140, 40), 70, 300)),   # human-ish speed
                    float(np.clip(rng.normal(35, 15), 8, 100)),
                    float(np.clip(rng.normal(3500, 800), 2000, 7000)),  # targets high-value
                    float(np.clip(rng.normal(22, 10), 5, 60)),
                    float(np.clip(rng.normal(6, 2), 2, 12)),
                    float(rng.uniform(0.0, 0.2)),
                ]
            elif pattern == "session_hijack":
                # Hijacked session: sudden new IP/device mid-session, known time
                row = [
                    int(rng.integers(8, 22)),      # normal business hours (more deceptive)
                    int(rng.random() < 0.3),
                    1,                             # new IP (attacker intercepts session)
                    1,                             # new device
                    int(rng.integers(0, 2)),       # few failures (has the token)
                    float(np.clip(rng.normal(120, 35), 60, 280)),
                    float(np.clip(rng.normal(25, 10), 5, 70)),
                    float(np.clip(rng.normal(4500, 1000), 2000, 9000)),  # high-value cart
                    float(np.clip(rng.normal(20, 8), 5, 55)),
                    float(np.clip(rng.normal(2.5, 1), 1, 6)),
                    float(rng.uniform(0.0, 0.1)),
                ]
            elif pattern == "insider_threat":
                # Internal actor: known device but anomalous behavior (off-hours, large tx)
                row = [
                    int(rng.integers(0, 6)),       # midnight / early AM
                    int(rng.random() < 0.4),
                    0,                             # known IP (internal)
                    0,                             # known device
                    0,
                    float(np.clip(rng.normal(130, 40), 60, 300)),
                    float(np.clip(rng.normal(30, 12), 5, 90)),
                    float(np.clip(rng.normal(9000, 2000), 5000, 15000)),  # very large tx
                    float(np.clip(rng.normal(25, 10), 5, 60)),
                    float(np.clip(rng.normal(3, 1), 1, 7)),
                    0.0,                           # legitimate UA
                ]
            elif pattern == "midnight_fraud":
                # Fraud at night: small-to-medium amounts, lots of requests
                row = [
                    int(rng.integers(0, 4)),
                    0, 1, 1,
                    int(rng.integers(2, 6)),
                    float(np.clip(rng.normal(110, 30), 55, 250)),
                    float(np.clip(rng.normal(22, 8), 5, 60)),
                    float(np.clip(rng.normal(1500, 400), 800, 4000)),
                    float(np.clip(rng.normal(60, 20), 20, 120)),
                    float(np.clip(rng.normal(18, 5), 8, 30)),      # many sessions/day
                    float(rng.uniform(0.1, 0.5)),
                ]
            elif pattern == "mobile_bot":
                # Mobile bot — pretends to be mobile browser but robot-speed
                row = [
                    int(rng.integers(1, 6)),
                    0, 1, 1,
                    int(rng.integers(5, 15)),
                    float(np.clip(rng.normal(22, 6), 8, 40)),      # very fast (bot)
                    float(np.clip(rng.normal(2, 0.8), 0.5, 5)),    # very regular
                    0.0,
                    float(np.clip(rng.normal(350, 60), 250, 520)),  # high click rate
                    float(np.clip(rng.normal(38, 6), 25, 50)),
                    float(rng.uniform(0.55, 0.9)),                  # semi-suspicious UA
                ]
            else:  # vpn_attacker
                # Attacker hiding behind VPN: normal timing, new IP, real browser
                row = [
                    int(rng.integers(9, 20)),      # business hours (stealthy)
                    int(rng.random() < 0.35),
                    1,                             # new IP (VPN exit node)
                    0,                             # may reuse device
                    int(rng.integers(4, 10)),
                    float(np.clip(rng.normal(155, 45), 70, 350)),
                    float(np.clip(rng.normal(35, 14), 8, 100)),
                    float(np.clip(rng.normal(5500, 1200), 3000, 11000)),
                    float(np.clip(rng.normal(35, 12), 8, 90)),
                    float(np.clip(rng.normal(20, 5), 10, 35)),
                    float(rng.uniform(0.0, 0.15)),   # real browser via VPN
                ]
            rows.append(row)

    return np.array(rows, dtype=float)


# ══════════════════════════════════════════════════════════════════════════════
# Pure helper functions  (used by AnomalyDetector, also importable directly)
# ══════════════════════════════════════════════════════════════════════════════

def build_feature_vector(payload: dict) -> np.ndarray:
    """Public alias — build a (1, n_features) numpy array from a payload dict."""
    return _build_feature_vector(payload)


def _build_feature_vector(payload: dict) -> np.ndarray:
    row = [feat.clip(float(payload.get(feat.name, feat.default))) for feat in FEATURES]
    return np.array(row, dtype=float).reshape(1, -1)


def score_to_risk(score: float) -> str:
    return _score_to_risk(score)

def score_to_numeric(score: float) -> int:
    return _score_to_numeric(score)


def _score_to_risk(score: float) -> str:
    """Map a raw IF score to a risk-level string."""
    if score < THRESHOLD_CRITICAL:
        return "critical"
    if score < THRESHOLD_HIGH:
        return "high"
    if score < THRESHOLD_MEDIUM:
        return "medium"
    return "low"


def _score_to_numeric(score: float) -> int:
    """
    Map raw IF score to 0-100 danger scale.
    IF scores typically span [-0.5, +0.5]:
      +0.5 → 0 (very normal)
      -0.5 → 100 (very anomalous)
    """
    risk = int((-score + 0.5) * 100)
    return max(0, min(100, risk))


def _build_explanation(
    payload: dict,
    raw_score: float,
    contributions: Dict[str, float],
) -> List[str]:
    """
    Generate a list of human-readable anomaly reasons.

    Combines rule-based triggers (hard-coded thresholds on individual features)
    with contribution-score evidence (features with unusually high deviation).
    """
    reasons: List[str] = []

    hour   = float(payload.get("hour_of_day", 12))
    ks     = float(payload.get("keystroke_speed_ms", 150))
    kir    = float(payload.get("keystroke_irregularity", 30))
    fails  = float(payload.get("failed_attempts", 0))
    tx     = float(payload.get("transaction_amount", 0))
    vel    = float(payload.get("session_velocity", 2))
    ua_r   = float(payload.get("ua_risk_score", 0))

    # ── Rule-based triggers ──────────────────────────────────────────────────
    if int(payload.get("is_new_ip", 0)) and fails >= 2:
        reasons.append("New IP address — login from an unrecognized location with multiple failures")
    if int(payload.get("is_new_device", 0)) and (fails >= 3 or hour < 5):
        reasons.append("New device fingerprint — unrecognized device combined with other risk factors")
    if fails >= 4:
        reasons.append(f"Multiple failed login attempts ({int(fails)} in the last hour)")
    if 0 < ks < 40:
        reasons.append(f"Extremely fast keystrokes ({ks:.0f} ms/key) — possible bot or script")
    if kir < 5 and ks > 0:
        reasons.append("Unusually regular keystroke rhythm — suggests automated tool")
    if hour < 4:
        reasons.append(f"Login at {int(hour)}:00 AM — suspicious off-hours activity")
    if tx > 4500:
        reasons.append(f"Large cart value ({tx:,.0f} k-VND) combined with suspicious session")
    if vel >= 15:
        reasons.append(f"High session velocity ({int(vel)} login attempts in last 24h) — possible automated attack")
    if ua_r >= 0.6:
        reasons.append("Suspicious user-agent detected — possible bot, script, or automated tool")

    # ── Contribution-based evidence (top deviating features) ─────────────────
    HIGH_CONTRIB = 0.68   # threshold to include in explanation
    top_contribs = sorted(
        [(k, v) for k, v in contributions.items() if v >= HIGH_CONTRIB],
        key=lambda kv: kv[1], reverse=True,
    )
    CONTRIB_LABELS = {
        "hour_of_day":           "Unusual login hour",
        "failed_attempts":       "Abnormally high failure count",
        "keystroke_speed_ms":    "Unusual keystroke speed",
        "keystroke_irregularity":"Unusual keystroke rhythm",
        "transaction_amount":    "Unusual transaction amount",
        "is_new_ip":             "Unrecognized IP address",
        "is_new_device":         "Unrecognized device",
        "session_velocity":      "Abnormally high login frequency",
        "ua_risk_score":         "Suspicious user-agent signature",
        "click_count_per_min":   "Abnormal click rate (bot-like speed)",
    }
    for feat_name, contrib in top_contribs:
        label = CONTRIB_LABELS.get(feat_name)
        if label and label not in " ".join(reasons):
            reasons.append(f"{label} (anomaly deviation: {contrib:.0%})")

    # ── Model-level signal ───────────────────────────────────────────────────
    if raw_score < THRESHOLD_CRITICAL:
        reasons.append("Isolation Forest + LOF ensemble: extremely strong anomaly signal")
    elif raw_score < THRESHOLD_HIGH:
        reasons.append("Isolation Forest + LOF ensemble: session outside normal login region")

    return reasons


def _recommendation(risk_level: str) -> str:
    MESSAGES = {
        "low":      "✅ Normal behavior — login allowed directly.",
        "medium":   "⚠️  Slightly unusual behavior — monitoring continues, 3FA not required.",
        "high":     "🔴 Anomaly detected! Third-factor authentication (3FA) required.",
        "critical": "🚨 Extremely high risk! Forcing 3FA — consider locking the account.",
    }
    return MESSAGES.get(risk_level, "Unknown risk level")


def _print_training_report(metrics: dict) -> None:
    print("─" * 55)
    print("  Isolation Forest — Training Report")
    print("─" * 55)
    print(f"  Normal samples  : {metrics['n_normal']}")
    print(f"  Anomaly samples : {metrics['n_anomaly']}")
    print(f"  Detection rate  : {metrics['detection_rate']:.1%}")
    print(f"  False alarm rate: {metrics['false_alarm_rate']:.1%}")
    print(f"  Normal  score   : {metrics['normal_score_mean']:.4f} ± {metrics['normal_score_std']:.4f}")
    print(f"  Anomaly score   : {metrics['anomaly_score_mean']:.4f} ± {metrics['anomaly_score_std']:.4f}")
    print("─" * 55)


# ══════════════════════════════════════════════════════════════════════════════
# Standalone CLI
# ══════════════════════════════════════════════════════════════════════════════

if __name__ == "__main__":
    import argparse, json

    parser = argparse.ArgumentParser(description="Isolation Forest Login Risk Engine")
    sub = parser.add_subparsers(dest="cmd")

    sub.add_parser("train", help="Train and save the engine")

    p_score = sub.add_parser("score", help="Score a session from JSON")
    p_score.add_argument("--payload", type=str, required=True,
                         help='JSON string, e.g. \'{"hour_of_day": 3, "is_new_ip": 1}\'')

    p_eval = sub.add_parser("evaluate", help="Print evaluation metrics")
    p_eval.add_argument("--n-normal",  type=int, default=1200)
    p_eval.add_argument("--n-anomaly", type=int, default=80)

    p_info = sub.add_parser("info", help="Print feature importances")

    args = parser.parse_args()

    logging.basicConfig(level=logging.WARNING, format="%(levelname)s | %(message)s")

    if args.cmd == "train":
        engine = AnomalyDetector()
        engine.train_and_save()
        print("Engine saved to engine.pkl (and legacy model.pkl updated).")

    elif args.cmd == "score":
        payload = json.loads(args.payload)
        engine  = AnomalyDetector.get_instance()
        result  = engine.score_session(payload)
        print(json.dumps(result.to_dict(), ensure_ascii=False, indent=2))

    elif args.cmd == "evaluate":
        X_n, X_a = generate_training_data(args.n_normal, args.n_anomaly)
        engine    = AnomalyDetector.get_instance()
        metrics   = engine.evaluate(X_n, X_a)
        _print_training_report(metrics)

    elif args.cmd == "info":
        engine = AnomalyDetector.get_instance()
        print("\nFeature importances (sensitivity-based):")
        for name, imp in engine.feature_importances().items():
            bar = "█" * int(imp * 5000)
            print(f"  {name:<28}  {imp:.6f}  {bar}")

    else:
        parser.print_help()
