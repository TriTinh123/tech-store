"""
Adaptive 3FA -- AI Risk Scoring API  (FastAPI)
==============================================
Powered by ai_risk_engine.AnomalyDetector (Isolation Forest).

Endpoints:
  GET  /health   -- liveness probe
  POST /score    -- analyse a login session, return risk assessment
  POST /decide   -- full 3FA decision (AI score + policy rules + user history)
  POST /train    -- re-train the Isolation Forest on fresh synthetic data
  GET  /features -- list feature names and specs

Docs (Swagger UI): http://127.0.0.1:5001/docs

Run:
  pip install -r requirements.txt
  python app.py            # or: uvicorn app:app --reload --port 5001
"""

from __future__ import annotations

import logging
import time
import httpx
from contextlib import asynccontextmanager
from typing import Dict, List, Optional

import uvicorn
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel, ConfigDict, Field

from ai_risk_engine import AnomalyDetector, FEATURE_NAMES, FEATURES
from security_controller import (
    SecurityController,
    SecurityPolicy,
    UserContext,
    AuthDecision,
    get_controller,
    reset_controller,
)

logging.basicConfig(level=logging.INFO, format="%(levelname)s | %(message)s")
logger = logging.getLogger(__name__)

# -- Singletons (loaded once at startup) -------------------------------------
_engine: Optional[AnomalyDetector] = None
_controller: Optional[SecurityController] = None


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Load engine + security controller before accepting requests."""
    global _engine, _controller
    logger.info("Loading Isolation Forest engine...")
    _engine = AnomalyDetector.get_instance()
    _controller = get_controller()
    logger.info("AI Risk Engine + SecurityController ready on http://127.0.0.1:5001")
    yield


app = FastAPI(
    title="Adaptive 3FA -- AI Risk Engine",
    description=(
        "Isolation Forest anomaly detection for e-commerce login sessions.\n\n"
        "**Risk levels**: low -> medium -> high -> critical\n\n"
        "**3FA is triggered** when risk_level is `high` or `critical`.\n\n"
        "Use `/score` for the raw AI assessment or `/decide` for the full\n"
        "policy decision (includes device trust, IP rules, user history).\n\n"
        "Powered by `ai_risk_engine` + `security_controller`."
    ),
    version="4.0.0",
    lifespan=lifespan,
)


# -- Pydantic schemas ----------------------------------------------------------

class LoginSession(BaseModel):
    model_config = ConfigDict(
        json_schema_extra={
            "example": {
                "user_id": 5,
                "hour_of_day": 3,
                "is_weekend": 0,
                "is_new_ip": 1,
                "is_new_device": 1,
                "failed_attempts": 5,
                "keystroke_speed_ms": 15.0,
                "keystroke_irregularity": 1.8,
                "transaction_amount": 7500,
                "click_count_per_min": 380,
            }
        }
    )

    user_id: int = Field(default=0, description="User ID (0 = anonymous)")
    hour_of_day: int = Field(default=12, ge=0, le=23, description="Hour of login (0-23)")
    is_weekend: int = Field(default=0, ge=0, le=1, description="1 if Saturday or Sunday")
    is_new_ip: int = Field(default=0, ge=0, le=1, description="1 if IP never seen for this account")
    is_new_device: int = Field(default=0, ge=0, le=1, description="1 if device fingerprint is new")
    failed_attempts: int = Field(default=0, ge=0, le=20, description="Failed logins in last hour")
    keystroke_speed_ms: float = Field(default=150.0, ge=0, description="Avg ms between keystrokes")
    keystroke_irregularity: float = Field(default=30.0, ge=0, description="Std-dev of keystroke intervals")
    transaction_amount: float = Field(default=0.0, ge=0, description="Cart value in 1 000 VND units")
    click_count_per_min: float = Field(default=30.0, ge=0, description="Mouse/keyboard clicks per minute on login page")
    session_velocity: float = Field(default=2.0, ge=0, le=50, description="Login attempts (all devices) in last 24 hours")
    ua_risk_score: float = Field(default=0.0, ge=0, le=1, description="User-agent risk score: 0=normal browser, 1=bot/suspicious")


class RiskResponse(BaseModel):
    risk_score: float = Field(description="Raw IF score (positive=normal, negative=anomaly)")
    risk_numeric: int = Field(description="0-100 danger scale (100=max risk)")
    risk_level: str = Field(description="low | medium | high | critical")
    is_anomaly: bool
    requires_3fa: bool
    explanation: List[str] = Field(description="Human-readable anomaly reasons")
    recommendation: str
    feature_contributions: Dict[str, float] = Field(
        default_factory=dict,
        description="Per-feature suspicion score (0-1)"
    )
    latency_ms: float = Field(default=0.0, description="Scoring latency in milliseconds")


class DecisionRequest(BaseModel):
    """Combined AI feature + policy context payload for the /decide endpoint."""
    model_config = ConfigDict(
        json_schema_extra={
            "example": {
                "user_id": 5,
                "ip": "203.0.113.9",
                "device_id": "fp-abc123",
                "hour_of_day": 3,
                "is_weekend": 0,
                "is_new_ip": 1,
                "is_new_device": 1,
                "failed_attempts": 3,
                "keystroke_speed_ms": 18.0,
                "keystroke_irregularity": 1.5,
                "transaction_amount": 8000,
                "click_count_per_min": 350,
                "has_security_question": True,
                "has_biometric": False,
                "account_age_days": 45,
            }
        }
    )

    # -- AI feature fields (forwarded to AnomalyDetector) ---------------------
    user_id: int = Field(default=0, description="Database user ID (0 = anonymous)")
    hour_of_day: int = Field(default=12, ge=0, le=23)
    is_weekend: int = Field(default=0, ge=0, le=1)
    is_new_ip: int = Field(default=0, ge=0, le=1)
    is_new_device: int = Field(default=0, ge=0, le=1)
    failed_attempts: int = Field(default=0, ge=0, le=20)
    keystroke_speed_ms: float = Field(default=150.0, ge=0)
    keystroke_irregularity: float = Field(default=30.0, ge=0)
    transaction_amount: float = Field(default=0.0, ge=0)
    click_count_per_min: float = Field(default=30.0, ge=0)
    session_velocity: float = Field(default=2.0, ge=0, le=50, description="Login attempts in last 24 hours")
    ua_risk_score: float = Field(default=0.0, ge=0, le=1, description="User-agent risk score")

    # -- Policy context fields (consumed by SecurityController) ---------------
    ip: str = Field(default="", description="Remote IP address")
    device_id: str = Field(default="", description="Device fingerprint")
    has_security_question: bool = Field(default=True)
    has_biometric: bool = Field(default=False)
    account_age_days: int = Field(default=365, ge=0)


class DecisionResponse(BaseModel):
    """Full 3FA auth decision returned by /decide."""
    action: str = Field(description="allow | require_3fa | deny")
    reason: str = Field(description="Why this decision was reached")
    method: str = Field(
        description=(
            "hard_rule | trust_bypass | new_device_policy "
            "| adaptive_escalation | ai_rule | default"
        )
    )
    challenge_type: Optional[str] = Field(
        default=None,
        description="3fa_otp | security_question | biometric | null",
    )
    risk_level: str
    risk_numeric: int
    requires_3fa: bool
    is_locked_out: bool
    explanation: List[str] = Field(default_factory=list)
    recommendations: List[str] = Field(default_factory=list)
    metadata: Dict = Field(default_factory=dict)
    latency_ms: float = 0.0


# -- Endpoints -----------------------------------------------------------------

@app.get("/health", summary="Liveness probe")
def health():
    """Returns engine status."""
    return {
        "status": "ok",
        "engine_loaded": _engine is not None and _engine.is_fitted,
    }


@app.post("/score", response_model=RiskResponse, summary="Analyse login session risk")
def score(session: LoginSession):
    """
    Submit a login-session feature vector and receive a full risk assessment.

    The Isolation Forest was trained on **normal** login patterns only (unsupervised).
    Sessions in the anomaly region trigger `requires_3fa=true`.
    """
    if _engine is None or not _engine.is_fitted:
        raise HTTPException(status_code=503, detail="Engine not loaded yet -- try again shortly")

    try:
        result = _engine.score_session(session.model_dump())
        return RiskResponse(**result.to_dict())
    except Exception as exc:
        logger.exception("Error scoring session")
        raise HTTPException(status_code=500, detail=str(exc))


@app.post("/train", summary="Re-train Isolation Forest engine")
def retrain():
    """
    Trigger a full re-train of the engine on fresh synthetic data.
    Updates engine.pkl and legacy model.pkl on disk.
    """
    global _engine, _controller
    try:
        engine = AnomalyDetector()
        engine.train_and_save()
        AnomalyDetector.reset_instance()
        reset_controller()
        _engine = AnomalyDetector.get_instance()
        _controller = get_controller()
        return {"status": "trained", "message": "AnomalyDetector retrained and reloaded"}
    except Exception as exc:
        logger.exception("Re-train failed")
        raise HTTPException(status_code=500, detail=str(exc))


@app.get("/features", summary="List feature specs")
def features():
    """Return the ordered list of input features with defaults and bounds."""
    return [
        {
            "name":        f.name,
            "default":     f.default,
            "low":         f.low,
            "high":        f.high,
            "description": f.description,
        }
        for f in FEATURES
    ]


@app.post("/decide", response_model=DecisionResponse, summary="Full 3FA auth decision")
def decide(req: DecisionRequest):
    """
    One-call endpoint that combines the **AI risk score** with the
    **SecurityController policy engine** to return a concrete auth decision.

    Unlike `/score` (which returns only the raw AI assessment), `/decide`
    applies additional rule layers:
    - Hard deny: blocked IPs, brute-force lockout
    - Trust bypass: whitelisted IPs, known device
    - New-device policy: always prompt 3FA on first-seen device
    - Adaptive escalation: lower threshold when user has a history of anomalies
    - AI rule: trigger 3FA when risk_level is `high` or `critical`

    The response includes which decision path fired (`method`) and the
    recommended 3FA challenge type (`challenge_type`).
    """
    if _engine is None or not _engine.is_fitted:
        raise HTTPException(status_code=503, detail="Engine not loaded yet — try again shortly")
    if _controller is None:
        raise HTTPException(status_code=503, detail="SecurityController not initialised")

    try:
        payload = req.model_dump()
        decision = _controller.evaluate_payload(payload)
        return DecisionResponse(**decision.to_dict())
    except Exception as exc:
        logger.exception("Error in /decide")
        raise HTTPException(status_code=500, detail=str(exc))


@app.delete("/decide/history/{user_id}", summary="Clear adaptive risk history for a user")
def clear_history(user_id: int):
    """
    Reset the rolling risk-score history for *user_id*.
    Call this after a successful 3FA completion to clear the anomaly counter.
    """
    if _controller is None:
        raise HTTPException(status_code=503, detail="SecurityController not initialised")
    _controller.clear_user_history(user_id)
    return {"status": "cleared", "user_id": user_id}


@app.get("/decide/history/{user_id}", summary="Get adaptive risk history for a user")
def get_history(user_id: int):
    """Return the rolling risk-score summary for *user_id* (admin use)."""
    if _controller is None:
        raise HTTPException(status_code=503, detail="SecurityController not initialised")
    return _controller.get_user_risk_summary(user_id)


# -- Entry point ---------------------------------------------------------------

# ── GeoIP (free ip-api.com) ──────────────────────────────────────────────────
_GEO_CACHE: Dict[str, dict] = {}   # simple in-memory cache

def _geoip_lookup(ip: str) -> dict:
    """Return geolocation dict for an IP.  Falls back to empty dict on error."""
    if ip in _GEO_CACHE:
        return _GEO_CACHE[ip]
    # ip-api.com free tier: 45 req/min, no key needed
    try:
        r = httpx.get(
            f"http://ip-api.com/json/{ip}",
            params={"fields": "status,country,countryCode,regionName,city,lat,lon,isp,query"},
            timeout=3.0,
        )
        if r.status_code == 200:
            data = r.json()
            if data.get("status") == "success":
                _GEO_CACHE[ip] = data
                return data
    except Exception as exc:
        logger.warning("GeoIP lookup failed for %s: %s", ip, exc)
    return {}


class GeoIpResponse(BaseModel):
    ip: str
    country: str = ""
    country_code: str = ""
    region: str = ""
    city: str = ""
    lat: float = 0.0
    lon: float = 0.0
    isp: str = ""
    is_vn: bool = False
    is_known_vpn_country: bool = False


@app.get("/geoip/{ip}", response_model=GeoIpResponse, summary="GeoIP lookup for an IP address")
def geoip(ip: str):
    """
    Return geolocation for *ip*.  Uses ip-api.com free tier (no key needed).
    `is_vn=True` when country_code is VN (Vietnam).
    `is_known_vpn_country=True` for high-risk VPN origin countries.
    """
    data = _geoip_lookup(ip)
    cc = data.get("countryCode", "")
    # Countries commonly associated with VPN/proxy abuse in this demo context
    vpn_countries = {"CN", "RU", "KP", "IR", "SY", "BY", "CU"}
    return GeoIpResponse(
        ip=ip,
        country=data.get("country", ""),
        country_code=cc,
        region=data.get("regionName", ""),
        city=data.get("city", ""),
        lat=data.get("lat", 0.0),
        lon=data.get("lon", 0.0),
        isp=data.get("isp", ""),
        is_vn=(cc == "VN"),
        is_known_vpn_country=(cc in vpn_countries),
    )


# ── Demo endpoint: scenario A (normal) vs B (anomaly) ─────────────────────────
class DemoScenario(BaseModel):
    """Preset session for demo purposes."""
    label: str
    description: str
    session: dict


DEMO_SCENARIOS = {
    "normal": DemoScenario(
        label="Kịch bản A — Người dùng hợp lệ",
        description=(
            "Đăng nhập lúc 10:00 sáng thứ Hai, IP quen thuộc (Việt Nam), "
            "thiết bị cũ, gõ phím tự nhiên, giỏ hàng trị giá 800,000 VND."
        ),
        session={
            "user_id": 1,
            "hour_of_day": 10,
            "is_weekend": 0,
            "is_new_ip": 0,
            "is_new_device": 0,
            "failed_attempts": 0,
            "keystroke_speed_ms": 145.0,
            "keystroke_irregularity": 28.0,
            "transaction_amount": 800,
            "click_count_per_min": 25.0,
        },
    ),
    "anomaly": DemoScenario(
        label="Kịch bản B — Kẻ tấn công / Bất thường",
        description=(
            "Đăng nhập lúc 3:00 sáng, IP nước ngoài (chưa từng thấy), "
            "thiết bị mới, tốc độ gõ phím bất thường nhanh (Bot), "
            "giỏ hàng đột biến 15,000,000 VND."
        ),
        session={
            "user_id": 1,
            "hour_of_day": 3,
            "is_weekend": 1,
            "is_new_ip": 1,
            "is_new_device": 1,
            "failed_attempts": 4,
            "keystroke_speed_ms": 12.0,
            "keystroke_irregularity": 2.1,
            "transaction_amount": 15000,
            "click_count_per_min": 400.0,
        },
    ),
}


@app.get("/demo", summary="Run both demo scenarios and return side-by-side results")
def demo():
    """
    Runs Scenario A (normal) and Scenario B (anomaly) through the AI engine
    and returns a side-by-side comparison.  Used by the admin demo page.
    """
    if _engine is None:
        raise HTTPException(status_code=503, detail="AI engine not loaded")

    results = {}
    for key, scenario in DEMO_SCENARIOS.items():
        t0 = time.perf_counter()
        risk = _engine.score_session(scenario.session)
        latency = round((time.perf_counter() - t0) * 1000, 2)
        results[key] = {
            "label": scenario.label,
            "description": scenario.description,
            "session": scenario.session,
            "risk_score": round(risk.risk_score, 4),
            "risk_numeric": risk.risk_numeric,
            "risk_level": risk.risk_level,
            "is_anomaly": risk.is_anomaly,
            "requires_3fa": risk.requires_3fa,
            "explanation": risk.explanation,
            "feature_contributions": {k: round(v, 3) for k, v in risk.feature_contributions.items()},
            "latency_ms": latency,
        }
    return {"scenarios": results, "engine_version": "Isolation Forest v4.0"}


# ── Audit-log behavior analysis endpoint ─────────────────────────────────────

class AuditFeatureRequest(BaseModel):
    """
    Aggregated login behavior features — không phải raw log.
    Chỉ gửi số liệu tổng hợp để tiết kiệm token tối đa.
    """
    model_config = ConfigDict(
        json_schema_extra={
            "example": {
                "failed_attempt": 7,
                "ip_count": 3,
                "device_count": 2,
                "time_window_min": 10,
                "geo_changed": 1,
                "user_id": 5,
            }
        }
    )
    failed_attempt:  int   = Field(default=0, ge=0, description="Số lần sai mật khẩu trong time_window")
    ip_count:        int   = Field(default=1, ge=1, description="Số IP khác nhau trong time_window")
    device_count:    int   = Field(default=1, ge=1, description="Số thiết bị khác nhau trong time_window")
    time_window_min: int   = Field(default=10, ge=1, description="Cửa sổ thời gian (phút)")
    geo_changed:     int   = Field(default=0, ge=0, le=1, description="1 nếu quốc gia thay đổi")
    user_id:         int   = Field(default=0, description="User ID (0 = ẩn danh)")


class AuditAnalysisResponse(BaseModel):
    result:     str  = Field(description="normal | suspicious | attack")
    risk_score: int  = Field(description="0-100")
    reasons:    List[str]
    action:     str  = Field(description="none | send_email | lock_account")
    features:   dict = Field(default_factory=dict)


@app.post(
    "/audit-log/analyze",
    response_model=AuditAnalysisResponse,
    summary="Phân tích hành vi đăng nhập từ audit log (token-optimized)",
)
def audit_analyze(req: AuditFeatureRequest):
    """
    Nhận feature đã gom nhóm (batch), chạy rule-engine + AI để phân loại:
    - **normal**: hoạt động bình thường
    - **suspicious**: đáng ngờ → gửi email cảnh báo
    - **attack**: tấn công → khóa tài khoản + alert admin

    ## Tối ưu chi phí token
    - Chỉ nhận 5 con số tổng hợp (KHÔNG nhận raw log)
    - Rule fallback tránh gọi AI với case rõ ràng
    - AI chỉ dùng cho case mơ hồ (failed_attempt 3-9)
    """
    f = req.model_dump()
    reasons: List[str] = []
    risk = 0

    # ── Rule fallback (không cần AI scoring) ─────────────────────────────
    if f["failed_attempt"] >= 10:
        return AuditAnalysisResponse(
            result="attack", risk_score=100,
            reasons=[f"Brute-force: {f['failed_attempt']} lần sai trong {f['time_window_min']} phút"],
            action="lock_account", features=f,
        )

    if f["ip_count"] >= 5:
        reasons.append(f"IP thay đổi liên tục: {f['ip_count']} IP khác nhau")
        risk += 40

    if f["device_count"] >= 4:
        reasons.append(f"Thiết bị đổi liên tục: {f['device_count']} device")
        risk += 30

    if f["geo_changed"] == 1:
        reasons.append("Đăng nhập từ quốc gia khác (geo change)")
        risk += 20

    # ── AI scoring cho case mơ hồ ─────────────────────────────────────────
    if f["failed_attempt"] >= 3 and _engine is not None and _engine.is_fitted:
        try:
            # Map sang LoginSession features để chạy qua Isolation Forest
            session_payload = {
                "user_id":               f["user_id"],
                "hour_of_day":           12,
                "is_weekend":            0,
                "is_new_ip":             min(f["ip_count"] - 1, 1),
                "is_new_device":         min(f["device_count"] - 1, 1),
                "failed_attempts":       f["failed_attempt"],
                "keystroke_speed_ms":    150.0,
                "keystroke_irregularity": 30.0,
                "transaction_amount":    0.0,
                "click_count_per_min":   30.0,
                "session_velocity":      f["failed_attempt"] / max(f["time_window_min"] / 60, 0.1),
                "ua_risk_score":         0.3,
            }
            ai_result = _engine.score_session(session_payload)
            risk += ai_result.risk_numeric // 2  # blend AI score
            if ai_result.is_anomaly:
                reasons.extend(ai_result.explanation)
        except Exception as exc:
            logger.warning("audit_analyze: AI scoring failed — %s", exc)

    # ── Rule: failed_attempt contributes to risk ──────────────────────────
    if f["failed_attempt"] >= 5:
        reasons.append(f"{f['failed_attempt']} lần sai mật khẩu")
        risk += 25
    elif f["failed_attempt"] >= 3:
        reasons.append(f"{f['failed_attempt']} lần sai mật khẩu")
        risk += 10

    risk = min(100, risk)

    if risk >= 70:
        result, action = "attack",     "lock_account"
    elif risk >= 40:
        result, action = "suspicious", "send_email"
    else:
        result, action = "normal",     "none"

    if not reasons:
        reasons = ["Hành vi trong ngưỡng bình thường"]

    return AuditAnalysisResponse(
        result=result, risk_score=risk,
        reasons=reasons, action=action, features=f,
    )


if __name__ == "__main__":
    uvicorn.run("app:app", host="127.0.0.1", port=5001, reload=False)