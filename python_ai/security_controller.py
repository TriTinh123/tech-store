"""
security_controller.py — 3FA Decision Engine
=============================================

This module sits between ai_risk_engine.AnomalyDetector and the application
layer (FastAPI / Laravel).  It translates raw AI risk scores into concrete
auth decisions: **allow** / **require_3fa** / **deny**.

Decision logic (evaluated in priority order, highest first):
┌──────────────────────────────────────────────┬──────────────────────────────┐
│ Condition                                    │ Decision                     │
├──────────────────────────────────────────────┼──────────────────────────────┤
│ ctx.ip is in blocked_ips                     │ DENY  (hard_rule)            │
│ failed_attempts >= lockout_threshold         │ DENY  (hard_rule)            │
│ ctx.ip is in trusted_ips                     │ ALLOW (trust_bypass)         │
│ anomaly_count >= escalation_count AND        │ REQUIRE_3FA (adaptive)       │
│   score < THRESHOLD_MEDIUM * factor          │                              │
│ known device + trusted_device_bypass         │ ALLOW (trust_bypass)         │
│   (unless risk_level == 'critical')          │                              │
│ new device + force_3fa_new_device            │ REQUIRE_3FA (new_device)     │
│ risk_level in {'high', 'critical'}           │ REQUIRE_3FA (ai_rule)        │
│ default                                      │ ALLOW (default)              │
└──────────────────────────────────────────────┴──────────────────────────────┘

Additionally the controller:
  • Selects the appropriate 3FA challenge type (3fa_otp / security_question
    / biometric) based on risk level and user capabilities.
  • Maintains a per-user rolling risk-score history (in-memory, with optional
    JSON persistence) that drives adaptive threshold escalation.
  • Exposes a module-level singleton via get_controller() and a one-call
    evaluate_login() convenience wrapper.

Quick usage
-----------
    from security_controller import SecurityController, SecurityPolicy, UserContext

    ctrl = SecurityController()
    ctx  = UserContext(user_id=42, ip="203.0.113.4", device_id="dev-abc",
                       is_new_device=True)
    # risk_result from AnomalyDetector.get_instance().score_session(...)
    decision = ctrl.evaluate(ctx, risk_result)
    print(decision.action)          # "allow" | "require_3fa" | "deny"
    print(decision.challenge_type)  # "3fa_otp" | "security_question" | "biometric" | None

    # Or use the one-call convenience wrapper:
    from security_controller import evaluate_login
    decision = evaluate_login({"user_id": 7, "ip": "...", "is_new_device": 1, ...})
"""

from __future__ import annotations

import json
import logging
import os
import time
from collections import deque
from dataclasses import dataclass, field
from typing import Deque, Dict, List, Optional, Set

from ai_risk_engine import (
    AnomalyDetector,
    RiskResult,
    THRESHOLD_CRITICAL,
    THRESHOLD_HIGH,
    THRESHOLD_MEDIUM,
    FA3_LEVELS,
)

logger = logging.getLogger(__name__)

# Path for optional disk persistence of per-user risk history
HISTORY_PATH = os.path.join(os.path.dirname(__file__), "user_risk_history.json")


# ══════════════════════════════════════════════════════════════════════════════
# Configuration
# ══════════════════════════════════════════════════════════════════════════════

@dataclass
class SecurityPolicy:
    """
    Behaviour parameters for SecurityController.

    Attributes
    ----------
    lockout_threshold : int
        Hard-deny when failed_attempts >= this value (default 10).
    trusted_ips : Set[str]
        IPs that bypass 3FA regardless of risk score.
    blocked_ips : Set[str]
        IPs that are hard-denied regardless of everything else.
    trusted_device_bypass : bool
        Skip 3FA for a *known* device (is_new_device=False) unless the AI
        rates the session as 'critical'.  Default True.
    force_3fa_new_device : bool
        Always require 3FA when is_new_device=True, even if the AI says
        'low' risk.  Default True.
    history_window : int
        Size of the rolling per-user risk-score window (default 10 sessions).
    adaptive_escalation_count : int
        How many anomaly-region scores must appear in the history window before
        the adaptive threshold kicks in (default 3).
    adaptive_escalation_factor : float
        Multiply THRESHOLD_HIGH by this factor to obtain a stricter effective
        threshold during adaptive escalation (default 0.5 → threshold = 0.00).
    persist_history : bool
        Write user_risk_history.json to disk after every decision (default False).
    """
    lockout_threshold: int = 7           # HARDENED: 10 → 7
    trusted_ips: Set[str] = field(default_factory=set)
    blocked_ips: Set[str] = field(default_factory=set)
    trusted_device_bypass: bool = True
    force_3fa_new_device: bool = True
    history_window: int = 15             # HARDENED: 10 → 15 (longer memory)
    adaptive_escalation_count: int = 2   # HARDENED: 3 → 2 (escalate sooner)
    adaptive_escalation_factor: float = 1.0
    persist_history: bool = True         # HARDENED: now persists to disk


# ══════════════════════════════════════════════════════════════════════════════
# Input — caller-supplied user/session context
# ══════════════════════════════════════════════════════════════════════════════

@dataclass
class UserContext:
    """
    Caller-supplied information about the user and their current session.

    These fields *complement* the AI engine's feature vector — they carry
    policy-relevant data (IP, device identity, account capabilities) that
    should not influence the Isolation Forest score directly.

    Attributes
    ----------
    user_id : int
        Database user ID.  0 is treated as "anonymous / not yet known".
    ip : str
        Remote IP address of the request.
    device_id : str
        Device fingerprint (e.g. hashed User-Agent + stored cookie value).
    failed_attempts : int
        Consecutive failed login attempts for this user in the last hour.
    is_new_device : bool
        True if this device fingerprint has never been seen for this account.
    is_new_ip : bool
        True if this IP has never been seen for this account.
    account_age_days : int
        Days since the account was created (0 = brand-new account).
    has_security_question : bool
        Whether the user has registered a security question/answer pair.
    has_biometric : bool
        Whether the user has registered biometric data (face / fingerprint).
    """
    user_id: int = 0
    ip: str = ""
    device_id: str = ""
    failed_attempts: int = 0
    is_new_device: bool = False
    is_new_ip: bool = False
    account_age_days: int = 365
    has_security_question: bool = True
    has_biometric: bool = False


# ══════════════════════════════════════════════════════════════════════════════
# Output — structured auth decision
# ══════════════════════════════════════════════════════════════════════════════

@dataclass
class AuthDecision:
    """
    The final security decision returned by SecurityController.evaluate().

    Attributes
    ----------
    action : str
        ``'allow'``        — grant access immediately.
        ``'require_3fa'``  — present a 3FA challenge before granting access.
        ``'deny'``         — reject the login attempt.
    reason : str
        Human-readable explanation of *why* this specific decision was reached.
    method : str
        Which decision path fired:
        ``'hard_rule'`` | ``'trust_bypass'`` | ``'new_device_policy'``
        | ``'adaptive_escalation'`` | ``'ai_rule'`` | ``'default'``
    challenge_type : Optional[str]
        ``'3fa_otp'`` | ``'security_question'`` | ``'biometric'`` | ``None``
        Only set when ``action == 'require_3fa'``.
    risk_level : str
        Forwarded from RiskResult: ``'low'`` | ``'medium'`` | ``'high'`` | ``'critical'``.
    risk_numeric : int
        0-100 danger scale forwarded from RiskResult (100 = maximum risk).
    requires_3fa : bool
        Convenience alias: ``True`` when ``action == 'require_3fa'``.
    is_locked_out : bool
        ``True`` when ``action == 'deny'``.
    explanation : List[str]
        Human-readable anomaly reasons forwarded from RiskResult.
    recommendations : List[str]
        Action items aimed at the user or administrator.
    metadata : Dict
        Extra fields useful for audit logging (user_id, ip, anomaly_count, …).
    latency_ms : float
        Wall-clock time for the full evaluate() call in milliseconds.
    """
    action: str                       # "allow" | "require_3fa" | "deny"
    reason: str
    method: str
    challenge_type: Optional[str]
    risk_level: str
    risk_numeric: int
    requires_3fa: bool
    is_locked_out: bool
    explanation: List[str] = field(default_factory=list)
    recommendations: List[str] = field(default_factory=list)
    metadata: Dict = field(default_factory=dict)
    latency_ms: float = 0.0

    def to_dict(self) -> dict:
        return {
            "action":          self.action,
            "reason":          self.reason,
            "method":          self.method,
            "challenge_type":  self.challenge_type,
            "risk_level":      self.risk_level,
            "risk_numeric":    self.risk_numeric,
            "requires_3fa":    self.requires_3fa,
            "is_locked_out":   self.is_locked_out,
            "explanation":     self.explanation,
            "recommendations": self.recommendations,
            "metadata":        self.metadata,
            "latency_ms":      round(self.latency_ms, 2),
        }


# ══════════════════════════════════════════════════════════════════════════════
# Internal helpers
# ══════════════════════════════════════════════════════════════════════════════

def _select_challenge(risk_level: str, ctx: UserContext) -> str:
    """
    Choose the most appropriate 3FA challenge method for a session.

    Priority:
      1. Biometric  — best UX + highest assurance; only for critical risk
                      where the user has registered biometric data.
      2. Security question — good balance; used when available.
      3. OTP (email/SMS)  — universal fallback.
    """
    if risk_level == "critical" and ctx.has_biometric:
        return "biometric"
    if ctx.has_security_question:
        return "security_question"
    return "3fa_otp"


def _build_recommendations(
    action: str,
    risk_level: str,
    ctx: UserContext,
    anomaly_count: int,
) -> List[str]:
    """Compose a list of actionable recommendations for the user / admin."""
    recs: List[str] = []
    if action == "deny":
        recs.append(
            "Tài khoản tạm thời bị khoá. Vui lòng liên hệ quản trị viên "
            "hoặc đặt lại mật khẩu."
        )
    if risk_level in ("high", "critical"):
        recs.append(
            "Phiên đăng nhập từ thiết bị hoặc địa chỉ IP bất thường. "
            "Vui lòng hoàn thành xác thực bổ sung."
        )
    if ctx.is_new_ip:
        recs.append("Địa chỉ IP mới được phát hiện — hãy xác minh danh tính của bạn.")
    if ctx.failed_attempts >= 5:
        recs.append(
            f"Đã phát hiện {ctx.failed_attempts} lần đăng nhập thất bại gần đây."
        )
    if anomaly_count >= 3:
        recs.append(
            "Lịch sử phiên làm việc cho thấy hành vi bất thường liên tục."
        )
    if not recs:
        recs.append("Đăng nhập bình thường — không phát hiện vấn đề bảo mật.")
    return recs


# ══════════════════════════════════════════════════════════════════════════════
# SecurityController
# ══════════════════════════════════════════════════════════════════════════════

class SecurityController:
    """
    Translates AI risk scores + user context into concrete auth decisions.

    Instantiate with an optional ``SecurityPolicy`` to override defaults,
    then call ``evaluate(ctx, risk_result)`` for each login attempt.

    Parameters
    ----------
    policy : SecurityPolicy, optional
        Behaviour configuration.  Uses default SecurityPolicy() if omitted.

    Examples
    --------
    >>> ctrl = SecurityController()
    >>> ctx  = UserContext(user_id=1, ip="10.1.2.3", is_new_device=True)
    >>> risk = AnomalyDetector.get_instance().score_session({...})
    >>> decision = ctrl.evaluate(ctx, risk)
    >>> decision.action
    'require_3fa'
    """

    def __init__(self, policy: Optional[SecurityPolicy] = None) -> None:
        self._policy = policy or SecurityPolicy()
        # Per-user rolling risk-score history: user_id → deque[float]
        self._history: Dict[int, Deque[float]] = {}
        self._load_history()

    # ── Public API ────────────────────────────────────────────────────────────

    def evaluate(self, ctx: UserContext, risk: RiskResult) -> AuthDecision:
        """
        Main decision entry point.

        Given a ``UserContext`` (caller-supplied) and a ``RiskResult``
        (from the AI engine), return an ``AuthDecision`` that the application
        can act on immediately.

        The current session's risk score is recorded into the user's rolling
        history *before* the decision is made, so it counts towards adaptive
        threshold evaluation.

        Parameters
        ----------
        ctx : UserContext
            Context about the user and their current session.
        risk : RiskResult
            Output of ``AnomalyDetector.score_session()``.

        Returns
        -------
        AuthDecision
        """
        t0 = time.perf_counter()

        # Record score in history *first* so it counts in the current decision
        if ctx.user_id:
            self._record_risk(ctx.user_id, risk.risk_score)

        decision = self._decide(ctx, risk)
        decision.latency_ms = (time.perf_counter() - t0) * 1000

        if self._policy.persist_history and ctx.user_id:
            self._save_history()

        logger.info(
            "user=%d ip=%s action=%s method=%s risk=%s latency=%.1fms",
            ctx.user_id, ctx.ip,
            decision.action, decision.method,
            risk.risk_level, decision.latency_ms,
        )
        return decision

    def evaluate_payload(self, payload: dict) -> AuthDecision:
        """
        Convenience wrapper: extract ``UserContext`` from *payload*, score
        the session via the AI engine singleton, then call ``evaluate()``.

        *payload* may contain any ``LoginSession`` feature keys plus the
        optional context keys: ``user_id``, ``ip``, ``device_id``,
        ``has_security_question``, ``has_biometric``, ``account_age_days``.

        Parameters
        ----------
        payload : dict
            Flat dict combining AI feature fields and user context fields.

        Returns
        -------
        AuthDecision
        """
        ctx = UserContext(
            user_id=int(payload.get("user_id", 0)),
            ip=str(payload.get("ip", "")),
            device_id=str(payload.get("device_id", "")),
            failed_attempts=int(payload.get("failed_attempts", 0)),
            is_new_device=bool(payload.get("is_new_device", False)),
            is_new_ip=bool(payload.get("is_new_ip", False)),
            account_age_days=int(payload.get("account_age_days", 365)),
            has_security_question=bool(payload.get("has_security_question", True)),
            has_biometric=bool(payload.get("has_biometric", False)),
        )
        engine = AnomalyDetector.get_instance()
        risk = engine.score_session(payload)
        return self.evaluate(ctx, risk)

    # ── Decision pipeline ─────────────────────────────────────────────────────

    def _decide(self, ctx: UserContext, risk: RiskResult) -> AuthDecision:
        """
        Run the decision pipeline against context + risk result.

        Evaluates conditions in strict priority order so that safety-critical
        rules always override lower-priority ones.
        """
        pol = self._policy
        anomaly_count = self._anomaly_count(ctx.user_id)

        # ── Priority 1: Hard deny — IP blocklist ──────────────────────────────
        if ctx.ip and ctx.ip in pol.blocked_ips:
            return self._deny(
                reason=f"IP address {ctx.ip!r} is on the security blocklist.",
                method="hard_rule",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 1: Hard deny — brute-force lockout ───────────────────────
        if ctx.failed_attempts >= pol.lockout_threshold:
            return self._deny(
                reason=(
                    f"Account locked after {ctx.failed_attempts} consecutive "
                    f"failed attempts (threshold: {pol.lockout_threshold})."
                ),
                method="hard_rule",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 2: Trust bypass — whitelisted IP ─────────────────────────
        if ctx.ip and ctx.ip in pol.trusted_ips:
            return self._allow(
                reason=f"IP address {ctx.ip!r} is on the trusted whitelist.",
                method="trust_bypass",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 3: Adaptive threshold escalation ─────────────────────────
        # Checked BEFORE device-trust bypass so that users with a pattern of
        # anomalous sessions cannot skip 3FA just by using a familiar device.
        if ctx.user_id and anomaly_count >= pol.adaptive_escalation_count:
            # Raise the effective 3FA threshold from HIGH (0.00) up towards
            # MEDIUM (0.08), scaled by adaptive_escalation_factor.
            # factor=1.0  → require 3FA for any medium+ risk
            # factor=0.5  → require 3FA for scores below 0.04
            effective_threshold = THRESHOLD_MEDIUM * pol.adaptive_escalation_factor
            if risk.risk_score < effective_threshold:
                return self._require_3fa(
                    reason=(
                        f"Adaptive threshold triggered: "
                        f"{anomaly_count} anomalous session(s) in recent history "
                        f"(effective threshold raised to {effective_threshold:.3f}, "
                        f"current score: {risk.risk_score:.4f})."
                    ),
                    method="adaptive_escalation",
                    risk=risk, ctx=ctx, anomaly_count=anomaly_count,
                )

        # ── Priority 4: Trust bypass — known device (not critical) ────────────
        # HARDENED: skip bypass when there are multiple recent failures even on known device
        if (
            pol.trusted_device_bypass
            and not ctx.is_new_device
            and risk.risk_level != "critical"
            and ctx.failed_attempts < 4    # NEW: don't bypass when ≥4 failures
        ):
            return self._allow(
                reason="Recognised device with non-critical risk and no recent failures — 3FA bypassed.",
                method="trust_bypass",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 5: New-device policy ─────────────────────────────────────
        if pol.force_3fa_new_device and ctx.is_new_device:
            return self._require_3fa(
                reason="New device fingerprint detected — 3FA required by policy.",
                method="new_device_policy",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 6: AI risk-level rule ────────────────────────────────────
        if risk.requires_3fa:
            return self._require_3fa(
                reason=(
                    f"AI risk level is '{risk.risk_level}' "
                    f"(score {risk.risk_score:.4f})."
                ),
                method="ai_rule",
                risk=risk, ctx=ctx, anomaly_count=anomaly_count,
            )

        # ── Priority 7: Default — allow ───────────────────────────────────────
        return self._allow(
            reason=(
                f"Risk level '{risk.risk_level}' is within acceptable bounds "
                f"(score {risk.risk_score:.4f})."
            ),
            method="default",
            risk=risk, ctx=ctx, anomaly_count=anomaly_count,
        )

    # ── Decision builders ─────────────────────────────────────────────────────

    def _deny(
        self,
        reason: str,
        method: str,
        risk: RiskResult,
        ctx: UserContext,
        anomaly_count: int,
    ) -> AuthDecision:
        return AuthDecision(
            action="deny",
            reason=reason,
            method=method,
            challenge_type=None,
            risk_level=risk.risk_level,
            risk_numeric=risk.risk_numeric,
            requires_3fa=False,
            is_locked_out=True,
            explanation=risk.explanation,
            recommendations=_build_recommendations(
                "deny", risk.risk_level, ctx, anomaly_count
            ),
            metadata=self._meta(ctx, anomaly_count),
        )

    def _allow(
        self,
        reason: str,
        method: str,
        risk: RiskResult,
        ctx: UserContext,
        anomaly_count: int,
    ) -> AuthDecision:
        return AuthDecision(
            action="allow",
            reason=reason,
            method=method,
            challenge_type=None,
            risk_level=risk.risk_level,
            risk_numeric=risk.risk_numeric,
            requires_3fa=False,
            is_locked_out=False,
            explanation=risk.explanation,
            recommendations=_build_recommendations(
                "allow", risk.risk_level, ctx, anomaly_count
            ),
            metadata=self._meta(ctx, anomaly_count),
        )

    def _require_3fa(
        self,
        reason: str,
        method: str,
        risk: RiskResult,
        ctx: UserContext,
        anomaly_count: int,
    ) -> AuthDecision:
        challenge = _select_challenge(risk.risk_level, ctx)
        meta = self._meta(ctx, anomaly_count)
        meta["challenge"] = challenge
        return AuthDecision(
            action="require_3fa",
            reason=reason,
            method=method,
            challenge_type=challenge,
            risk_level=risk.risk_level,
            risk_numeric=risk.risk_numeric,
            requires_3fa=True,
            is_locked_out=False,
            explanation=risk.explanation,
            recommendations=_build_recommendations(
                "require_3fa", risk.risk_level, ctx, anomaly_count
            ),
            metadata=meta,
        )

    @staticmethod
    def _meta(ctx: UserContext, anomaly_count: int) -> Dict:
        return {
            "user_id":       ctx.user_id,
            "ip":            ctx.ip,
            "device_id":     ctx.device_id,
            "anomaly_count": anomaly_count,
        }

    # ── Per-user history tracking ─────────────────────────────────────────────

    def _record_risk(self, user_id: int, score: float) -> None:
        """Append *score* to the rolling history deque for *user_id*."""
        if user_id not in self._history:
            self._history[user_id] = deque(maxlen=self._policy.history_window)
        self._history[user_id].append(score)

    def _anomaly_count(self, user_id: int) -> int:
        """
        Return the number of anomaly-region scores (score < THRESHOLD_HIGH)
        in the user's current history window.
        """
        if user_id not in self._history:
            return 0
        return sum(1 for s in self._history[user_id] if s < THRESHOLD_HIGH)

    def _load_history(self) -> None:
        """Load persisted history from disk (only when persist_history=True)."""
        if not self._policy.persist_history or not os.path.exists(HISTORY_PATH):
            return
        try:
            with open(HISTORY_PATH, "r", encoding="utf-8") as fh:
                raw: Dict[str, list] = json.load(fh)
            for uid_str, scores in raw.items():
                uid = int(uid_str)
                win = self._policy.history_window
                self._history[uid] = deque(scores[-win:], maxlen=win)
            logger.info("Loaded risk history for %d users.", len(self._history))
        except Exception:
            logger.warning("Could not load user risk history from %s.", HISTORY_PATH)

    def _save_history(self) -> None:
        """Flush user history to disk."""
        try:
            serialisable = {
                str(uid): list(scores)
                for uid, scores in self._history.items()
            }
            with open(HISTORY_PATH, "w", encoding="utf-8") as fh:
                json.dump(serialisable, fh, indent=2)
        except Exception:
            logger.warning("Could not save user risk history to %s.", HISTORY_PATH)

    # ── Introspection helpers (useful for admin dashboards) ───────────────────

    def get_user_history(self, user_id: int) -> List[float]:
        """Return the rolling risk-score list for *user_id* (most recent last)."""
        return list(self._history.get(user_id, []))

    def get_user_risk_summary(self, user_id: int) -> dict:
        """
        Return a summary dict for admin dashboards.

        Keys: user_id, sessions, anomalies, avg_score, last_score.
        """
        history = self.get_user_history(user_id)
        if not history:
            return {
                "user_id":    user_id,
                "sessions":   0,
                "anomalies":  0,
                "avg_score":  None,
                "last_score": None,
            }
        return {
            "user_id":    user_id,
            "sessions":   len(history),
            "anomalies":  sum(1 for s in history if s < THRESHOLD_HIGH),
            "avg_score":  round(sum(history) / len(history), 4),
            "last_score": round(history[-1], 4),
        }

    def clear_user_history(self, user_id: int) -> None:
        """
        Remove all history for *user_id*.
        Call this after a successful 3FA completion to reset the anomaly
        counter for that user.
        """
        self._history.pop(user_id, None)
        if self._policy.persist_history:
            self._save_history()


# ══════════════════════════════════════════════════════════════════════════════
# Module-level singleton + convenience wrapper
# ══════════════════════════════════════════════════════════════════════════════

_controller: Optional[SecurityController] = None


def get_controller(policy: Optional[SecurityPolicy] = None) -> SecurityController:
    """
    Return the module-level SecurityController singleton.

    Pass *policy* only on the first call (or after resetting) to configure
    the singleton.  Subsequent calls with *policy* are ignored.
    """
    global _controller
    if _controller is None:
        _controller = SecurityController(policy)
    return _controller


def reset_controller() -> None:
    """Force the singleton to be recreated on the next get_controller() call."""
    global _controller
    _controller = None


def evaluate_login(payload: dict) -> AuthDecision:
    """
    One-call convenience function.

    Accepts a flat dict that may contain AI feature fields *and* context fields
    (``user_id``, ``ip``, ``device_id``, ``has_security_question``,
    ``has_biometric``, ``account_age_days``).
    Internally calls the module-level SecurityController singleton.

    Returns
    -------
    AuthDecision

    Examples
    --------
    >>> from security_controller import evaluate_login
    >>> d = evaluate_login({
    ...     "user_id": 7, "ip": "203.0.113.9", "device_id": "new-dev",
    ...     "hour_of_day": 2, "is_new_ip": 1, "is_new_device": 1,
    ...     "failed_attempts": 3, "keystroke_speed_ms": 18,
    ...     "keystroke_irregularity": 1.5, "transaction_amount": 8000,
    ... })
    >>> print(d.action, d.challenge_type)
    require_3fa security_question
    """
    return get_controller().evaluate_payload(payload)


# ══════════════════════════════════════════════════════════════════════════════
# CLI
# ══════════════════════════════════════════════════════════════════════════════

def _run_demo() -> None:
    """Print a formatted table of decisions for representative scenarios."""
    import textwrap

    scenarios = [
        {
            "label": "Normal weekday login (known device & IP)",
            "payload": {
                "user_id": 1, "ip": "192.168.1.10", "device_id": "dev-a",
                "hour_of_day": 10, "is_weekend": 0, "is_new_ip": 0, "is_new_device": 0,
                "failed_attempts": 0, "keystroke_speed_ms": 140,
                "keystroke_irregularity": 25, "transaction_amount": 200,
                "has_security_question": True,
            },
        },
        {
            "label": "New device, night-time login",
            "payload": {
                "user_id": 2, "ip": "203.0.113.5", "device_id": "dev-new",
                "hour_of_day": 2, "is_weekend": 0, "is_new_ip": 0, "is_new_device": 1,
                "failed_attempts": 0, "keystroke_speed_ms": 120,
                "keystroke_irregularity": 20, "transaction_amount": 0,
                "has_security_question": True,
            },
        },
        {
            "label": "Bot-like (ultra-fast, new IP+device, high tx)",
            "payload": {
                "user_id": 3, "ip": "45.33.32.156", "device_id": "bot-dev",
                "hour_of_day": 3, "is_weekend": 0, "is_new_ip": 1, "is_new_device": 1,
                "failed_attempts": 7, "keystroke_speed_ms": 8,
                "keystroke_irregularity": 1.2, "transaction_amount": 9500,
                "has_security_question": True, "has_biometric": True,
            },
        },
        {
            "label": "Brute-force lockout (12 failures)",
            "payload": {
                "user_id": 4, "ip": "10.10.10.10", "device_id": "dev-b",
                "hour_of_day": 14, "is_weekend": 0, "is_new_ip": 0, "is_new_device": 0,
                "failed_attempts": 12, "keystroke_speed_ms": 130,
                "keystroke_irregularity": 22, "transaction_amount": 0,
                "has_security_question": True,
            },
        },
        {
            "label": "Trusted IP — bypass 3FA even with anomalous features",
            "payload": {
                "user_id": 5, "ip": "10.0.0.1", "device_id": "office-pc",
                "hour_of_day": 3, "is_weekend": 1, "is_new_ip": 1, "is_new_device": 1,
                "failed_attempts": 2, "keystroke_speed_ms": 20,
                "keystroke_irregularity": 1.8, "transaction_amount": 5000,
                "has_security_question": True,
            },
        },
        {
            "label": "Blocked IP",
            "payload": {
                "user_id": 6, "ip": "185.220.101.1", "device_id": "dev-c",
                "hour_of_day": 9, "is_weekend": 0, "is_new_ip": 1, "is_new_device": 0,
                "failed_attempts": 0, "keystroke_speed_ms": 100,
                "keystroke_irregularity": 15, "transaction_amount": 0,
                "has_security_question": True,
            },
        },
        {
            "label": "Adaptive escalation (user 7, known device, medium risk)",
            "payload": {
                # Known device (is_new_device=0) would normally bypass 3FA,
                # but user 7 has 3 anomalous sessions in history so the
                # adaptive threshold fires first.
                "user_id": 7, "ip": "198.51.100.9", "device_id": "dev-d",
                "hour_of_day": 2, "is_weekend": 0, "is_new_ip": 1, "is_new_device": 0,
                "failed_attempts": 2, "keystroke_speed_ms": 90,
                "keystroke_irregularity": 40, "transaction_amount": 1200,
                "has_security_question": True,
            },
        },
    ]

    policy = SecurityPolicy(
        trusted_ips={"10.0.0.1"},
        blocked_ips={"185.220.101.1"},
    )
    ctrl = SecurityController(policy)

    # Pre-seed three anomalous history entries for user 7 to trigger adaptive path
    ctrl._history[7] = deque(
        [THRESHOLD_CRITICAL - 0.05, THRESHOLD_CRITICAL - 0.02, THRESHOLD_HIGH - 0.01],
        maxlen=10,
    )

    col_w = (50, 13, 24, 10, 18)
    header = (
        f"{'Scenario':<{col_w[0]}} "
        f"{'Action':<{col_w[1]}} "
        f"{'Method':<{col_w[2]}} "
        f"{'Risk':<{col_w[3]}} "
        f"{'Challenge':<{col_w[4]}}"
    )
    sep = "=" * sum(col_w + (4,))

    print(f"\n{sep}")
    print("  SecurityController — Demo Scenarios")
    print(sep)
    print(header)
    print("-" * sum(col_w + (4,)))

    for s in scenarios:
        d = ctrl.evaluate_payload(s["payload"])
        label = textwrap.shorten(s["label"], width=col_w[0] - 2)
        challenge = d.challenge_type or "—"
        print(
            f"  {label:<{col_w[0] - 2}}  "
            f"{d.action:<{col_w[1]}} "
            f"{d.method:<{col_w[2]}} "
            f"{d.risk_level:<{col_w[3]}} "
            f"{challenge:<{col_w[4]}}"
        )

    print(f"{sep}\n")


if __name__ == "__main__":
    import argparse
    import sys

    parser = argparse.ArgumentParser(
        description="SecurityController CLI — test 3FA decision logic."
    )
    sub = parser.add_subparsers(dest="cmd")

    # ── evaluate ──
    ev = sub.add_parser("evaluate", help="Evaluate a login payload (JSON string).")
    ev.add_argument(
        "--payload", "-p", required=True,
        help='JSON string, e.g. \'{"user_id":1,"ip":"1.2.3.4","is_new_device":1}\'',
    )
    ev.add_argument(
        "--trusted-ips", nargs="*", default=[],
        help="IP addresses to add to the trusted whitelist",
    )
    ev.add_argument(
        "--blocked-ips", nargs="*", default=[],
        help="IP addresses to add to the blocklist",
    )

    # ── history ──
    hist = sub.add_parser("history", help="Show risk-score history for a user.")
    hist.add_argument("user_id", type=int)

    # ── demo ──
    sub.add_parser("demo", help="Run a battery of representative demo scenarios.")

    args = parser.parse_args()
    logging.basicConfig(level=logging.WARNING)

    if args.cmd == "evaluate":
        payload = json.loads(args.payload)
        policy = SecurityPolicy(
            trusted_ips=set(args.trusted_ips),
            blocked_ips=set(args.blocked_ips),
        )
        ctrl = SecurityController(policy)
        decision = ctrl.evaluate_payload(payload)
        print(json.dumps(decision.to_dict(), indent=2, ensure_ascii=False))

    elif args.cmd == "history":
        ctrl = get_controller()
        print(json.dumps(ctrl.get_user_risk_summary(args.user_id), indent=2))

    elif args.cmd == "demo":
        _run_demo()

    else:
        parser.print_help()
        sys.exit(1)
