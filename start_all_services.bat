@echo off
setlocal EnableDelayedExpansion

set "PROJECT_DIR=C:\xampp\htdocs\plant_app"
set "AI_DIR=%PROJECT_DIR%\ai_service"
set "ENV_FILE=%PROJECT_DIR%\.env"
set "CF_TUNNEL_TOKEN="
set "PUBLIC_BASE_URL="

if exist "%ENV_FILE%" (
    for /f "usebackq tokens=1,* delims==" %%A in ("%ENV_FILE%") do (
        if /I "%%A"=="CF_TUNNEL_TOKEN" set "CF_TUNNEL_TOKEN=%%B"
        if /I "%%A"=="PUBLIC_BASE_URL" set "PUBLIC_BASE_URL=%%B"
    )
)

echo ===================================================
echo           Flora AI Backend Auto Starter
echo ===================================================
echo.

call :ensure_port 80 "Apache" "C:\xampp\apache_start.bat"
call :ensure_port 3306 "MySQL" "C:\xampp\mysql_start.bat"
call :ensure_ai
call :ensure_tunnel

echo.
echo Backend status check complete.
echo ===================================================
echo.
exit /b 0

:ensure_port
set "PORT=%~1"
set "NAME=%~2"
set "START_CMD=%~3"

netstat -ano | findstr ":%PORT%" | findstr "LISTENING" >nul
if %errorlevel%==0 (
    echo [OK] %NAME% already running on port %PORT%.
    exit /b 0
)

echo [INFO] Starting %NAME%...
start "" /min cmd /c "call "%START_CMD%""
timeout /t 3 /nobreak >nul

netstat -ano | findstr ":%PORT%" | findstr "LISTENING" >nul
if %errorlevel%==0 (
    echo [OK] %NAME% started on port %PORT%.
) else (
    echo [WARN] %NAME% may not have started. Please check XAMPP control panel.
)
exit /b 0

:ensure_ai
netstat -ano | findstr ":5001" | findstr "LISTENING" >nul
if %errorlevel%==0 (
    echo [OK] Flora AI service already running on port 5001.
    exit /b 0
)

echo [INFO] Starting Flora AI service...
if exist "%AI_DIR%\app.py" (
        if exist "C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" (
            start "FloraAI" /min cmd /c "cd /d "%AI_DIR%" && "C:\Users\Administrator\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" app.py >> ai_log.txt 2>> ai_err.txt"
        ) else (
            start "FloraAI" /min cmd /c "cd /d "%AI_DIR%" && python app.py >> ai_log.txt 2>> ai_err.txt"
        )

    set /a retries=0
    :wait_ai
    timeout /t 1 /nobreak >nul
    netstat -ano | findstr ":5001" | findstr "LISTENING" >nul
    if %errorlevel%==0 (
        echo [OK] Flora AI service started on port 5001.
        exit /b 0
    )
    set /a retries+=1
    if !retries! LSS 15 goto wait_ai

    echo [WARN] AI service did not start within timeout. Check:
    echo        %AI_DIR%\ai_err.txt
) else (
    echo [WARN] Missing app.py at %AI_DIR%.
)
exit /b 0

:ensure_tunnel
if "%CF_TUNNEL_TOKEN%"=="" (
    echo [INFO] Cloudflare tunnel token not found in .env. Skipping public tunnel startup.
    exit /b 0
)

set "CLOUDFLARED_EXE=cloudflared"
where cloudflared >nul 2>nul
if not %errorlevel%==0 (
    if exist "C:\Program Files (x86)\cloudflared\cloudflared.exe" (
        set "CLOUDFLARED_EXE=C:\Program Files (x86)\cloudflared\cloudflared.exe"
    ) else if exist "C:\Program Files\cloudflared\cloudflared.exe" (
        set "CLOUDFLARED_EXE=C:\Program Files\cloudflared\cloudflared.exe"
    ) else (
        echo [WARN] cloudflared not found. Install it, then tunnel can start automatically.
        echo        Install command: winget install --id Cloudflare.cloudflared
        exit /b 0
    )
)

tasklist | findstr /I "cloudflared.exe" >nul
if %errorlevel%==0 (
    echo [OK] Cloudflare tunnel already running.
    if not "%PUBLIC_BASE_URL%"=="" echo [INFO] Public URL: %PUBLIC_BASE_URL%
    exit /b 0
)

echo [INFO] Starting Cloudflare tunnel...
start "FloraAITunnel" /min cmd /c "\"%CLOUDFLARED_EXE%\" tunnel --no-autoupdate run --token %CF_TUNNEL_TOKEN% >> \"%PROJECT_DIR%\ai_service\tunnel_log.txt\" 2>> \"%PROJECT_DIR%\ai_service\tunnel_err.txt\""
timeout /t 3 /nobreak >nul

tasklist | findstr /I "cloudflared.exe" >nul
if %errorlevel%==0 (
    echo [OK] Cloudflare tunnel started.
    if not "%PUBLIC_BASE_URL%"=="" echo [INFO] Public URL: %PUBLIC_BASE_URL%
) else (
    echo [WARN] Cloudflare tunnel did not start. Check:
    echo        %PROJECT_DIR%\ai_service\tunnel_err.txt
)
exit /b 0
