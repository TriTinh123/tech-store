"""
Adaptive 3FA — AI Risk Scoring Layer
Algorithm: Isolation Forest (anomaly detection)

Features per login session:
  1. hour_of_day          – 0-23
  2. is_weekend           – 0/1
  3. is_new_ip            – 0/1  (IP never seen for this account)
  4. is_new_device        – 0/1  (device fingerprint never seen)
  5. failed_attempts      – count of recent failed logins (0-10)
  6. keystroke_speed_ms   – avg ms between keystrokes (higher = slower = human)
  7. keystroke_irregularity – std-dev of keystroke intervals (0 = bot, high = human)
  8. transaction_amount   – basket value in 1000 VND (0 = no transaction)

Risk levels returned:
  critical  → score < -0.15   (definite anomaly)
  high      → score < 0.0
  medium    → score < 0.08
  low       → score >= 0.08
"""

import numpy as np
from sklearn.ensemble import IsolationForest
import joblib, os, json

MODEL_PATH = os.path.join(os.path.dirname(__file__), 'model.pkl')

# Feature order (must match what the API receives)
FEATURES = [
    'hour_of_day',
    'is_weekend',
    'is_new_ip',
    'is_new_device',
    'failed_attempts',
    'keystroke_speed_ms',
    'keystroke_irregularity',
    'transaction_amount',
]


def build_feature_vector(data: dict) -> np.ndarray:
    """Convert a dict of raw inputs to a numpy row vector."""
    row = [
        float(data.get('hour_of_day', 12)),
        float(data.get('is_weekend', 0)),
        float(data.get('is_new_ip', 0)),
        float(data.get('is_new_device', 0)),
        float(data.get('failed_attempts', 0)),
        float(data.get('keystroke_speed_ms', 150)),
        float(data.get('keystroke_irregularity', 30)),
        float(data.get('transaction_amount', 0)),
    ]
    return np.array(row).reshape(1, -1)


def score_to_risk(score: float) -> str:
    """Convert Isolation Forest anomaly score to a risk-level string."""
    if score < -0.15:
        return 'critical'
    if score < 0.0:
        return 'high'
    if score < 0.08:
        return 'medium'
    return 'low'


def score_to_numeric(score: float) -> int:
    """Convert IF score to a 0-100 risk number (100 = most dangerous)."""
    # IF scores typically range [-0.5, 0.5]
    # Map: 0.5 → 0, -0.5 → 100
    risk = int((-score + 0.5) * 100)
    return max(0, min(100, risk))


# ─── Training ──────────────────────────────────────────────────────────────────

def generate_normal_samples(n: int = 1200) -> np.ndarray:
    """Generate synthetic 'normal' login sessions."""
    rng = np.random.default_rng(42)
    data = []
    for _ in range(n):
        hour              = rng.integers(7, 22)          # business hours
        is_weekend        = 1 if rng.random() < 0.25 else 0
        is_new_ip         = 1 if rng.random() < 0.05 else 0  # rarely from new IP
        is_new_device     = 1 if rng.random() < 0.03 else 0
        failed_attempts   = rng.integers(0, 2)
        keystroke_speed   = rng.normal(150, 40)          # human typing ~100-250 ms
        keystroke_irr     = rng.normal(35, 10)           # natural variation
        tx_amount         = rng.normal(700, 300)         # 400k-1M VND typical
        tx_amount         = max(0, tx_amount)
        data.append([hour, is_weekend, is_new_ip, is_new_device,
                     failed_attempts, keystroke_speed, keystroke_irr, tx_amount])
    return np.array(data)


def generate_anomaly_samples(n: int = 80) -> np.ndarray:
    """Generate synthetic 'anomalous' login sessions (for evaluation only)."""
    rng = np.random.default_rng(99)
    data = []
    for _ in range(n):
        # Mix of suspicious patterns
        pattern = rng.integers(0, 4)
        if pattern == 0:   # bot — very fast typing, off-hours, new IP
            hour, is_weekend       = rng.integers(0, 5), 0
            is_new_ip, is_new_device = 1, 1
            failed_attempts        = rng.integers(3, 8)
            keystroke_speed        = rng.normal(20, 5)     # very fast = bot
            keystroke_irr          = rng.normal(2, 1)      # perfectly regular
            tx_amount              = 0
        elif pattern == 1: # credential stuffing — many failures
            hour                   = rng.integers(1, 4)
            is_weekend, is_new_ip  = 0, 1
            is_new_device          = 1
            failed_attempts        = rng.integers(6, 10)
            keystroke_speed        = rng.normal(30, 10)
            keystroke_irr          = rng.normal(5, 2)
            tx_amount              = 0
        elif pattern == 2: # big transaction from new device
            hour                   = rng.integers(3, 6)
            is_weekend, is_new_ip  = 0, 1
            is_new_device          = 1
            failed_attempts        = 0
            keystroke_speed        = rng.normal(80, 20)
            keystroke_irr          = rng.normal(10, 5)
            tx_amount              = rng.normal(5000, 1000)  # huge purchase
        else:              # time anomaly + new location
            hour                   = rng.integers(2, 5)
            is_weekend, is_new_ip  = 0, 1
            is_new_device          = 0
            failed_attempts        = rng.integers(2, 5)
            keystroke_speed        = rng.normal(100, 30)
            keystroke_irr          = rng.normal(20, 8)
            tx_amount              = rng.normal(2000, 500)

        data.append([max(0, hour), is_weekend, is_new_ip, is_new_device,
                     max(0, failed_attempts), max(1, keystroke_speed),
                     max(0, keystroke_irr), max(0, tx_amount)])
    return np.array(data)


def train_and_save():
    """Train an Isolation Forest on synthetic normal data and save to model.pkl."""
    print("Generating synthetic training data …")
    X_normal = generate_normal_samples(1200)
    X_anomaly = generate_anomaly_samples(80)   # not used for training, only evaluation

    # IsolationForest is trained on NORMAL data only (unsupervised)
    model = IsolationForest(
        n_estimators=200,
        max_samples='auto',
        contamination=0.05,    # ~5% expected anomalies in production
        random_state=42,
        n_jobs=-1,
    )
    model.fit(X_normal)

    # Quick evaluation
    scores_normal  = model.decision_function(X_normal)
    scores_anomaly = model.decision_function(X_anomaly)
    tp = (scores_anomaly < 0.0).sum()
    total_a = len(X_anomaly)
    print(f"Detection rate on anomaly samples: {tp}/{total_a} = {tp/total_a:.0%}")
    print(f"Normal mean score: {scores_normal.mean():.4f} ± {scores_normal.std():.4f}")
    print(f"Anomaly mean score: {scores_anomaly.mean():.4f} ± {scores_anomaly.std():.4f}")

    joblib.dump(model, MODEL_PATH)
    print(f"Model saved to {MODEL_PATH}")
    return model


def load_model():
    """Load model from disk, training first if not present."""
    if not os.path.exists(MODEL_PATH):
        print("model.pkl not found — training now …")
        return train_and_save()
    return joblib.load(MODEL_PATH)
