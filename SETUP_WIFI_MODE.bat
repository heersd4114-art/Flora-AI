@echo off
REM ============================================================================
REM Flora AI - WiFi Mode Setup (Works on Phone WITHOUT USB Cable)
REM ============================================================================
REM This script configures your phone to access the AI service via WiFi only
REM Your phone and laptop must be on the SAME WiFi network
REM ============================================================================

cls
echo.
echo ============================================================================
echo  Flora AI - WiFi MODE SETUP
echo ============================================================================
echo.
echo This will setup your phone to access AI service via WiFi.
echo Your phone and laptop MUST be on the same WiFi network.
echo.
pause

REM Kill any existing AI service on port 5001
echo.
echo [1/4] Stopping any existing AI service...
taskkill /FI "WINDOWTITLE eq Flora AI*" /T /F 2>nul
taskkill /IM python.exe /F 2>nul
timeout /t 2 /nobreak

REM Start Apache
echo.
echo [2/4] Starting Apache web server on port 80...
cd /d "%~dp0"
"%ProgramFiles%\XAMPP\apache\bin\apache.exe" -k start 2>nul
if errorlevel 1 (
    echo  [WARN] Apache might already be running
)
timeout /t 2 /nobreak

REM Start MySQL
echo.
echo [3/4] Starting MySQL database on port 3306...
"%ProgramFiles%\XAMPP\mysql\bin\mysqld.exe" 2>nul
if errorlevel 1 (
    echo  [WARN] MySQL might already be running
)
timeout /t 2 /nobreak

REM Start AI Service
echo.
echo [4/4] Starting Flora AI service on port 5001...
echo  This will load the local AI model (first time takes 30-60 seconds)...
echo.
cd /d "%~dp0\ai_service"
start "Flora AI Service" "C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" app.py

echo.
echo ============================================================================
echo SETUP COMPLETE!
echo ============================================================================
echo.
echo TO USE ON YOUR PHONE (WiFi Mode):
echo.
echo 1. Make sure your phone is on the SAME WiFi network as this laptop
echo 2. Open Flora AI app on your phone
echo 3. If it doesn't auto-connect, the app will show:
echo    "WAITING FOR API... Checking connection"
echo 4. Let it search for the server (20-30 seconds)
echo 5. Once connected, you can use plant scanning WITHOUT USB cable
echo.
echo IMPORTANT NOTES:
echo  - If Gemini API quota is exhausted (429 error):
echo    App will AUTOMATICALLY use local AI (ResNet-50)
echo  - Local AI is less accurate but ALWAYS available
echo  - WiFi range: Anywhere on same network (home/office WiFi)
echo  - Phone MUST be unlocked when using app
echo  - If disconnected, app will auto-reconnect
echo.
echo ============================================================================
echo.
pause
