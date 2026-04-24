@echo off
setlocal

set "PROJECT_DIR=C:\xampp\htdocs\plant_app"
set "ENV_FILE=%PROJECT_DIR%\.env"

echo ===================================================
echo      Cloudflare Tunnel One-Time Setup Helper
echo ===================================================
echo.
echo 1) Install cloudflared (run as Admin once):
echo    winget install --id Cloudflare.cloudflared
echo.
echo 2) In Cloudflare Zero Trust Dashboard, create a tunnel and
echo    copy the Tunnel Token + your public app URL.
echo.

set /p TUNNEL_TOKEN=Paste CF Tunnel Token: 
if "%TUNNEL_TOKEN%"=="" (
    echo [ERROR] Token is required.
    pause
    exit /b 1
)

set /p PUBLIC_URL=Paste Public API Base URL (example: https://api.yourdomain.com/plant_app/api): 
if "%PUBLIC_URL%"=="" (
    echo [ERROR] Public URL is required.
    pause
    exit /b 1
)

if not exist "%ENV_FILE%" (
    echo GEMINI_API_KEY=>> "%ENV_FILE%"
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$p='%ENV_FILE%';" ^
  "$c=Get-Content $p -ErrorAction SilentlyContinue;" ^
  "$c=$c | Where-Object {$_ -notmatch '^(CF_TUNNEL_TOKEN|PUBLIC_BASE_URL)='};" ^
  "$c += 'CF_TUNNEL_TOKEN=%TUNNEL_TOKEN%';" ^
  "$c += 'PUBLIC_BASE_URL=%PUBLIC_URL%';" ^
  "Set-Content -Path $p -Value $c -Encoding UTF8"

echo [OK] Saved CF_TUNNEL_TOKEN and PUBLIC_BASE_URL to .env
echo.
echo Next:
echo   1) Open plant_app_flutter\lib\config.dart
echo   2) Set Config.publicBaseUrl to your PUBLIC_BASE_URL value
echo   3) Rebuild Flutter app once
echo.
echo You can now run start_all_services.bat; tunnel will auto-start.
pause
