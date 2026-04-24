@echo off
REM ============================================================================
REM Flora AI - Quick Verification Script
REM ============================================================================

cls
echo.
echo ============================================================================
echo  FLORA AI - System Verification
echo ============================================================================
echo.

REM Check Apache
echo Checking Apache (port 80)...
netstat -ano 2>nul | findstr ":80 " >nul
if errorlevel 1 (
    echo  [OFFLINE] Apache NOT running
) else (
    echo  [OK] Apache is running
)

REM Check MySQL
echo.
echo Checking MySQL (port 3306)...
netstat -ano 2>nul | findstr ":3306 " >nul
if errorlevel 1 (
    echo  [OFFLINE] MySQL NOT running
) else (
    echo  [OK] MySQL is running
)

REM Check AI Service
echo.
echo Checking Flora AI Service (port 5001)...
netstat -ano 2>nul | findstr ":5001 " >nul
if errorlevel 1 (
    echo  [OFFLINE] Flora AI NOT running
    echo.
    echo Starting Flora AI service...
    cd /d "%~dp0\ai_service"
    start "Flora AI Service" python app.py
    echo Waiting 10 seconds for AI to start...
    timeout /t 10 /nobreak
) else (
    echo  [OK] Flora AI is running
)

REM Check API Connectivity
echo.
echo Testing API connectivity...
curl -s http://127.0.0.1/plant_app/api/products.php >nul 2>&1
if errorlevel 1 (
    echo  [ERROR] API not responding
) else (
    echo  [OK] API is responding
)

REM Check Database
echo.
echo Testing database...
curl -s http://127.0.0.1/plant_app/check_db.php | findstr "Database connected" >nul 2>&1
if errorlevel 1 (
    echo  [WARN] Database check failed or not responding
) else (
    echo  [OK] Database is connected
)

REM Summary
echo.
echo ============================================================================
echo VERIFICATION COMPLETE
echo ============================================================================
echo.
echo Next Steps:
echo 1. Make sure phone is on same WiFi network
echo 2. Open Flora AI app on your phone
echo 3. Try scanning a plant image
echo.
echo For troubleshooting, check:
echo  - ai_service/ai_log.txt (AI service logs)
echo  - ai_err.txt (Error logs)
echo  - Database connection status
echo.
pause
