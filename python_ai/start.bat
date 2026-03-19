@echo off
echo === Adaptive 3FA — Python AI Risk Scorer ===
echo.

cd /d "%~dp0"

REM Check Python
python --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Python not found. Install Python 3.10+ and add to PATH.
    pause
    exit /b 1
)

REM Install deps
pip install -r requirements.txt -q

REM Train model if not present
if not exist model.pkl (
    echo Training Isolation Forest model...
    python train.py
)

echo.
echo Starting Flask API on http://127.0.0.1:5001
python app.py
