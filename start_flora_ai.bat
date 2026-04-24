@echo off
title Flora AI Service
color 0A
echo.
echo  ============================================
echo   Flora AI Service - Startup
echo  ============================================
echo.
echo  Killing any old instances on port 5001...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :5001 ^| findstr LISTENING') do (
    taskkill /PID %%a /F 2>nul
)
timeout /t 2 /nobreak >nul

echo  Starting Flora AI Service...
echo  The service will run at http://127.0.0.1:5001
echo.
echo  NOTE: Keep this window open while using Flora AI.
echo        The AI takes 30-60 sec to analyse each image (normal).
echo.
cd /d "c:\xampp\htdocs\plant_app\ai_service"
"C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" app.py
pause
