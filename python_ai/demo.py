"""
demo.py  —  AI Anomaly Detection Demo
======================================
Simulates two login scenarios through the full AI risk pipeline and prints
a human-readable report showing the risk score and security action.

Scenarios
---------
  1. Normal User  — business hours, known device, familiar IP, typical cart.
  2. Suspicious   — 3 AM login, brand-new IP + device, bot-speed keystrokes,
                    high-value cart (potential fraud).

Usage
-----
    python demo.py                   # pretty terminal output
    python demo.py --json            # machine-readable JSON
"""

from __future__ import annotations

import argparse
import json
import sys
import time
import textwrap
from typing import Any

# ── Load engine & controller ─────────────────────────────────────────────────
import logging
logging.disable(logging.CRITICAL)          # silence training progress bars

from ai_risk_engine import AnomalyDetector, THRESHOLD_CRITICAL, THRESHOLD_HIGH, THRESHOLD_MEDIUM
from security_controller import SecurityController, SecurityPolicy

# ════════════════════════════════════════════════════════════════════════════
# Scenario definitions
# ════════════════════════════════════════════════════════════════════════════

SCENARIOS: list[dict[str, Any]] = [
    # ── Scenario 1: Normal user ───────────────────────────────────────────
    {
        "title":       "Scenario 1 — Normal User Login",
        "description": (
            "Logged in at 10 AM on a weekday from a previously seen IP and "
            "device. Typing speed and rhythm are natural. Small cart value."
        ),
        "payload": {
            "user_id":                1,
            "hour_of_day":            10,    # business hours
            "is_weekend":              0,
            "is_new_ip":               0,    # known IP
            "is_new_device":           0,    # known device
            "failed_attempts":         0,    # no recent failures
            "keystroke_speed_ms":    155.0,  # normal human typing (~155 ms/key)
            "keystroke_irregularity": 32.0,  # natural variation in rhythm
            "transaction_amount":    350.0,  # modest cart (350k VND)
        },
        "context": {
            "ip":                    "203.113.10.5",
            "device_id":             "fp-known-abc123",
            "is_new_device":          False,
            "is_new_ip":              False,
            "has_security_question":  True,
            "has_biometric":          False,
            "account_age_days":       365,
        },
        "expected_action": "allow",
    },

    # ── Scenario 2: Suspicious / Anomalous login ──────────────────────────
    {
        "title":       "Scenario 2 — Suspicious Login (Anomaly Detected)",
        "description": (
            "Login attempt at 3 AM from a brand-new IP and device. "
            "Keystrokes are extremely fast and perfectly regular "
            "(scripted bot pattern). Very large cart value from an "
            "unrecognised location."
        ),
        "payload": {
            "user_id":                1,
            "hour_of_day":            3,     # 3 AM — unusual hour
            "is_weekend":             0,
            "is_new_ip":              1,     # never-seen IP
            "is_new_device":          1,     # new device fingerprint
            "failed_attempts":        6,     # recent failures
            "keystroke_speed_ms":    14.0,   # bot-speed: 14 ms/key
            "keystroke_irregularity": 1.2,   # near-perfect regularity → script
            "transaction_amount":  7800.0,   # very large cart (7 800k VND)
        },
        "context": {
            "ip":                    "198.51.100.77",
            "device_id":             "fp-new-xyz999",
            "is_new_device":          True,
            "is_new_ip":              True,
            "has_security_question":  True,
            "has_biometric":          False,
            "account_age_days":       365,
        },
        "expected_action": "require_3fa",
    },
]

# ════════════════════════════════════════════════════════════════════════════
# ANSI colour codes (disabled automatically when not a TTY)
# ════════════════════════════════════════════════════════════════════════════

_USE_COLOUR = sys.stdout.isatty()

def _c(code: str, text: str) -> str:
    if not _USE_COLOUR:
        return text
    return f"\033[{code}m{text}\033[0m"

RED     = lambda t: _c("31;1", t)
YELLOW  = lambda t: _c("33;1", t)
GREEN   = lambda t: _c("32;1", t)
CYAN    = lambda t: _c("36;1", t)
BOLD    = lambda t: _c("1",    t)
DIM     = lambda t: _c("2",    t)

LEVEL_COLOUR = {
    "critical": RED,
    "high":     YELLOW,
    "medium":   lambda t: _c("33", t),
    "low":      GREEN,
}

ACTION_COLOUR = {
    "allow":       GREEN,
    "require_3fa": YELLOW,
    "deny":        RED,
}

# ════════════════════════════════════════════════════════════════════════════
# Bar helpers
# ════════════════════════════════════════════════════════════════════════════

def _bar(value: int, width: int = 30) -> str:
    """ASCII progress bar for a 0-100 numeric risk value."""
    filled = int(round(value / 100 * width))
    bar    = "█" * filled + "░" * (width - filled)
    return f"[{bar}] {value}/100"


def _threshold_ruler() -> str:
    """
    Visual ruler showing where the score sits on the IF score axis.
    Range spans −0.30 … +0.25 (55 chars wide).
    """
    left, right = -0.30, 0.25
    span  = right - left
    width = 55
    ruler = list("─" * width)

    for val, label, char in [
        (THRESHOLD_CRITICAL, "C", "▼"),
        (THRESHOLD_HIGH,     "H", "▼"),
        (THRESHOLD_MEDIUM,   "M", "▼"),
    ]:
        pos = int((val - left) / span * (width - 1))
        if 0 <= pos < width:
            ruler[pos] = char

    axis = "".join(ruler)
    labels = (
        f"  {DIM('-0.30')}"
        + " " * 12
        + RED("C")
        + " " * 8
        + YELLOW("H")
        + " " * 8
        + _c('33', "M")
        + " " * 6
        + GREEN("+0.25")
    )
    return (
        f"  Score axis:  {DIM('-0.30')} ←─── "
        + DIM("─" * 8)
        + RED("│C")
        + DIM("─" * 6)
        + YELLOW("│H")
        + DIM("─" * 6)
        + _c('33', "│M")
        + DIM("─" * 5)
        + " ──→")


def _score_marker(score: float) -> str:
    """Show where the score sits relative to the three thresholds."""
    left, right = -0.30, 0.25
    span  = right - left
    width = 40
    pos   = int((score - left) / span * (width - 1))
    pos   = max(0, min(width - 1, pos))
    ruler = list("·" * width)
    ruler[pos] = "▲"

    thresholds = {
        THRESHOLD_CRITICAL: "C",
        THRESHOLD_HIGH:     "H",
        THRESHOLD_MEDIUM:   "M",
    }
    for val, ch in thresholds.items():
        p2 = int((val - left) / span * (width - 1))
        if 0 <= p2 < width and ruler[p2] == "·":
            ruler[p2] = "|"

    return "  [" + "".join(ruler) + "]"


# ════════════════════════════════════════════════════════════════════════════
# Main runner
# ════════════════════════════════════════════════════════════════════════════

def run_scenario(
    scenario: dict[str, Any],
    engine: AnomalyDetector,
    controller: SecurityController,
) -> dict[str, Any]:
    """Score one scenario and return structured result dict."""
    t0 = time.perf_counter()

    # Step 1 — AI scoring
    result = engine.score_session(scenario["payload"])

    # Step 2 — Security policy decision
    from security_controller import UserContext
    ctx = UserContext(**scenario["context"], failed_attempts=scenario["payload"]["failed_attempts"])
    decision = controller.evaluate(ctx, result)

    elapsed = (time.perf_counter() - t0) * 1000

    return {
        "scenario":  scenario["title"],
        "risk": {
            "score":        result.risk_score,
            "numeric":      result.risk_numeric,
            "level":        result.risk_level,
            "is_anomaly":   result.is_anomaly,
            "requires_3fa": result.requires_3fa,
            "explanation":  result.explanation,
            "top_features": dict(sorted(
                result.feature_contributions.items(),
                key=lambda kv: kv[1], reverse=True
            )[:4]),
        },
        "decision": {
            "action":         decision.action,
            "method":         decision.method,
            "challenge_type": decision.challenge_type,
            "reason":         decision.reason,
            "recommendations": decision.recommendations,
        },
        "expected_action": scenario["expected_action"],
        "correct":         decision.action == scenario["expected_action"],
        "latency_ms":      round(elapsed, 2),
    }


def print_report(idx: int, scenario: dict[str, Any], res: dict[str, Any]) -> None:
    """Pretty-print one scenario result."""
    r   = res["risk"]
    dec = res["decision"]

    level_fn  = LEVEL_COLOUR.get(r["level"], BOLD)
    action_fn = ACTION_COLOUR.get(dec["action"], BOLD)

    sep = "═" * 64
    print(f"\n{BOLD(sep)}")
    print(f"  {BOLD(f'[{idx}]')}  {BOLD(res['scenario'])}")
    print(f"{BOLD(sep)}")

    # Description
    desc_lines = textwrap.wrap(scenario["description"], width=58)
    for line in desc_lines:
        print(f"  {DIM(line)}")
    print()

    # Input feature table
    p = scenario["payload"]
    print(f"  {BOLD('Input features')}")
    print(f"  {'─'*56}")
    rows = [
        ("Hour of day",              f"{p['hour_of_day']:02d}:00"),
        ("Weekend",                  "Yes" if p["is_weekend"] else "No"),
        ("New IP address",           RED("Yes (unseen)") if p["is_new_ip"]     else GREEN("No (known)")),
        ("New device",               RED("Yes (unseen)") if p["is_new_device"] else GREEN("No (known)")),
        ("Failed attempts (1 h)",    (RED if p["failed_attempts"] >= 3 else GREEN)(str(p["failed_attempts"]))),
        ("Keystroke speed (ms/key)", f"{p['keystroke_speed_ms']:.0f} ms" + (RED(" ← bot-speed") if p["keystroke_speed_ms"] < 40 else "")),
        ("Keystroke irregularity",   f"{p['keystroke_irregularity']:.1f}" + (RED(" ← near-perfect") if p["keystroke_irregularity"] < 5 else "")),
        ("Cart value (k VND)",       f"{p['transaction_amount']:,.0f}k" + (RED(" ← very large") if p["transaction_amount"] > 3000 else "")),
    ]
    for label, value in rows:
        print(f"  {label:<30} {value}")
    print()

    # Risk score
    print(f"  {BOLD('AI Risk Assessment')}")
    print(f"  {'─'*56}")
    score_str = f"{r['score']:+.4f}"
    print(f"  IF score (raw)    : {level_fn(score_str)}")
    print(f"  Risk level        : {level_fn(r['level'].upper())}")
    print(f"  Danger scale      : {level_fn(_bar(r['numeric']))}")
    print(f"  Is anomaly        : {RED('YES') if r['is_anomaly'] else GREEN('NO')}")
    print(f"  Requires 3FA      : {RED('YES') if r['requires_3fa'] else GREEN('NO')}")
    print()

    # Score position on axis
    print(f"  Score on threshold axis  (C=−0.15, H=0.00, M=+0.08)")
    print(_score_marker(r["score"]))
    print(f"   └ {DIM('critical')}─────{DIM('high')}────{DIM('medium')}──────{DIM('low')}")
    print()

    # Top contributing features
    if r["top_features"]:
        print(f"  {BOLD('Top suspicious features')}")
        print(f"  {'─'*56}")
        for feat, contrib in r["top_features"].items():
            bar_w = int(contrib * 20)
            bar   = "█" * bar_w + "░" * (20 - bar_w)
            colour = RED if contrib > 0.7 else (YELLOW if contrib > 0.4 else DIM)
            print(f"  {feat:<30} {colour(f'[{bar}] {contrib:.2f}')}")
        print()

    # Explanation
    if r["explanation"]:
        print(f"  {BOLD('Reasons detected')}")
        print(f"  {'─'*56}")
        for reason in r["explanation"]:
            print(f"  ⚠  {reason}")
        print()

    # Security decision
    print(f"  {BOLD('Security Decision')}")
    print(f"  {'─'*56}")
    print(f"  Action            : {action_fn(dec['action'].upper().replace('_', ' '))}")
    print(f"  Decision method   : {dec['method']}")
    if dec["challenge_type"]:
        print(f"  3FA challenge     : {dec['challenge_type']}")
    reason_lines = textwrap.wrap(dec["reason"], width=52)
    print(f"  Reason            : {reason_lines[0]}")
    for line in reason_lines[1:]:
        print(f"  {' '*20}{line}")
    if dec["recommendations"]:
        print(f"  Recommendation    : {dec['recommendations'][0]}")
    print()

    # Verdict
    verdict = GREEN("✓ CORRECT") if res["correct"] else RED("✗ WRONG")
    print(f"  Expected action: {res['expected_action']:<12}  Got: {action_fn(dec['action'])}  {verdict}")
    print(f"  Pipeline latency: {res['latency_ms']:.1f} ms")
    print()


def main() -> None:
    parser = argparse.ArgumentParser(description="AI 3FA anomaly detection demo")
    parser.add_argument("--json", action="store_true", help="Output raw JSON instead of pretty text")
    args = parser.parse_args()

    # Load singletons once
    print("Loading AI engine...", end="", flush=True)
    engine     = AnomalyDetector.get_instance()
    controller = SecurityController(SecurityPolicy(force_3fa_new_device=True))
    print(" ready.\n")

    results = []
    for i, scenario in enumerate(SCENARIOS, start=1):
        res = run_scenario(scenario, engine, controller)
        results.append(res)
        if not args.json:
            print_report(i, scenario, res)

    # Summary
    if args.json:
        print(json.dumps(results, indent=2, ensure_ascii=False))
    else:
        correct = sum(1 for r in results if r["correct"])
        total   = len(results)
        sep     = "═" * 64
        print(BOLD(sep))
        print(f"  {BOLD('DEMO SUMMARY')}")
        print(BOLD(sep))
        for r in results:
            status = GREEN("✓ PASS") if r["correct"] else RED("✗ FAIL")
            action = ACTION_COLOUR.get(r["decision"]["action"], BOLD)(r["decision"]["action"])
            level  = LEVEL_COLOUR.get(r["risk"]["level"], BOLD)(r["risk"]["level"])
            print(
                f"  {r['scenario']:<45} "
                f"risk={level:<8}  action={action:<12} {status}"
            )
        print(BOLD(sep))
        print(f"  Result: {GREEN(str(correct))}/{total} scenarios produced the expected security action.")
        print()

        print(f"  {BOLD('How to interpret the output')}")
        print(f"  {'─'*56}")
        print(f"  {GREEN('LOW / MEDIUM risk')}  — login proceeds immediately (no 3FA).")
        print(f"  {YELLOW('HIGH risk')}          — user is redirected to /auth/3fa.")
        print(f"  {RED('CRITICAL risk')}      — user is redirected to /auth/3fa (biometric")
        print(f"                        preferred when available).")
        print(f"  {RED('DENY')}               — account locked (brute-force threshold hit).")
        print()


if __name__ == "__main__":
    main()
