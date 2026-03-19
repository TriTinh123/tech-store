"""
train.py -- Train the Isolation Forest engine and save to disk.

Usage:
  cd python_ai
  python train.py

Produces:
  engine.pkl  -- full AnomalyDetector (model + training stats)
  model.pkl   -- legacy sklearn model for backward compatibility
"""
from ai_risk_engine import AnomalyDetector

if __name__ == "__main__":
    engine = AnomalyDetector()
    engine.train_and_save()
    print("Done. Start the API server with: python app.py")