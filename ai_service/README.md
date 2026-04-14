# Custom AI Microservice for Plant Disease Detection

This module provides a local Python-based AI backend for your Plant App.

## Features
1. **Hybrid Inference Engine**: Uses pre-trained MobileNetV2 for plant recognition combined with a heuristic algorithm for disease spotting (if no custom model is provided).
2. **Custom Training Capable**: Includes `train.py` to train your own Deep Learning model on a specific dataset.
3. **API Integration**: Seamlessly integrates with `process_identify.php` via standard HTTP calls (localhost:5000).

## How to Run
The system requires the Python Flask server to be running.
Run the following command in a new terminal:
```bash
python ai_service/app.py
```

## How to Train a Custom Model
1. Collect images of diseases.
2. Organize them into `ai_service/dataset/` (e.g., `dataset/Rust`, `dataset/Healthy`).
3. Run:
```bash
python ai_service/train.py
```
4. Similar to `custom_model.keras` will be created. The `app.py` service will automatically detect and use it next time you restart it.
