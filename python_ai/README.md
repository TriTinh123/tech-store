# Python AI — Risk Scoring Layer

Isolation Forest-based anomaly detection for Adaptive 3FA.

## Quick Start

```bash
cd python_ai
pip install -r requirements.txt
python train.py        # train model once (saved as model.pkl)
python app.py          # start Flask API on http://127.0.0.1:5001
```

On Windows you can just double-click **start.bat**.

## API

### `POST /score`
```json
{
  "user_id": 42,
  "hour_of_day": 2,
  "is_weekend": 0,
  "is_new_ip": 1,
  "is_new_device": 1,
  "failed_attempts": 4,
  "keystroke_speed_ms": 18,
  "keystroke_irregularity": 2,
  "transaction_amount": 0
}
```
Response:
```json
{
  "risk_score": -0.21,
  "risk_numeric": 71,
  "risk_level": "critical",
  "is_anomaly": true,
  "explanation": ["new_ip_address", "new_device", "multiple_failed_attempts", "bot_like_typing_speed", "unusual_login_hour"]
}
```

### `GET /health`
Returns `{"status":"ok"}`.

## Risk Levels
| Level    | IF Score       | Action                              |
|----------|----------------|-------------------------------------|
| low      | ≥ 0.08         | 2FA only (Password + OTP)           |
| medium   | 0.0 – 0.08     | 2FA only                            |
| high     | -0.15 – 0.0    | 3FA required (+ TOTP/BiometricCheck)|
| critical | < -0.15        | 3FA required + alert admin          |

## Features
| # | Feature                 | Description                              |
|---|-------------------------|------------------------------------------|
| 1 | hour_of_day             | 0-23 — midnight logins are suspicious    |
| 2 | is_weekend              | 0/1                                      |
| 3 | is_new_ip               | 0/1 — IP never seen for this account     |
| 4 | is_new_device           | 0/1 — device fingerprint never seen      |
| 5 | failed_attempts         | recent failed login count                |
| 6 | keystroke_speed_ms      | avg ms between keys (bots = very fast)   |
| 7 | keystroke_irregularity  | std-dev of keystroke intervals           |
| 8 | transaction_amount      | basket value in 1000 VND                 |
