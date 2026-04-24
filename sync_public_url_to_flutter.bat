@echo off
setlocal

set "PROJECT_DIR=C:\xampp\htdocs\plant_app"
set "ENV_FILE=%PROJECT_DIR%\.env"
set "CONFIG_FILE=%PROJECT_DIR%\plant_app_flutter\lib\config.dart"
set "PUBLIC_URL="

if not exist "%ENV_FILE%" (
    echo [ERROR] .env not found at %ENV_FILE%
    pause
    exit /b 1
)

for /f "usebackq tokens=1,* delims==" %%A in ("%ENV_FILE%") do (
    if /I "%%A"=="PUBLIC_BASE_URL" set "PUBLIC_URL=%%B"
)

if "%PUBLIC_URL%"=="" (
    echo [INFO] PUBLIC_BASE_URL is empty in .env
    set /p PUBLIC_URL=Enter PUBLIC_BASE_URL now (example: https://api.yourdomain.com/plant_app/api): 
    if "%PUBLIC_URL%"=="" (
        echo [ERROR] PUBLIC_BASE_URL is still empty. Cannot continue.
        pause
        exit /b 1
    )

    powershell -NoProfile -ExecutionPolicy Bypass -Command ^
      "$p='%ENV_FILE%';" ^
      "$c=Get-Content $p -ErrorAction SilentlyContinue;" ^
      "$c=$c | Where-Object {$_ -notmatch '^PUBLIC_BASE_URL='};" ^
      "$c += 'PUBLIC_BASE_URL=%PUBLIC_URL%';" ^
      "Set-Content -Path $p -Value $c -Encoding UTF8"

    echo [OK] Saved PUBLIC_BASE_URL to .env
)

powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$f='%CONFIG_FILE%';" ^
  "$u='%PUBLIC_URL%'.Replace('\\','/');" ^
  "$txt=Get-Content $f -Raw;" ^
  "$txt=[regex]::Replace($txt,'static const String publicBaseUrl = \".*?\";','static const String publicBaseUrl = \"'+$u+'\";');" ^
  "Set-Content -Path $f -Value $txt -Encoding UTF8"

echo [OK] Updated Config.publicBaseUrl in Flutter config.
echo Run Flutter build/run once to apply this public URL.
pause
