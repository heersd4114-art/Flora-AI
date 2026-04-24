@echo off
echo Starting Flora Ai Service (CPU Mode)...
set CUDA_VISIBLE_DEVICES=-1
cd /d d:\plant_app\ai_service
python app.py
pause
