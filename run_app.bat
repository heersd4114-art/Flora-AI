@echo off
set JAVA_HOME=d:\java\jdk-17.0.18+8
set PATH=%JAVA_HOME%\bin;%PATH%

echo ---------------------------------------------------
echo              FLORA AI LAUNCHER
echo ---------------------------------------------------
echo 1. Setting Environment Variables...
echo    JAVA_HOME = %JAVA_HOME%
echo.
echo 2. Starting backend services (Apache + MySQL + AI)...
call c:\xampp\htdocs\plant_app\start_all_services.bat

echo.
echo 3. Navigating to project c:\xampp\htdocs\plant_app\plant_app_flutter...
cd /d c:\xampp\htdocs\plant_app\plant_app_flutter

echo.
echo ---------------------------------------------------
echo 3. AUTO-SYNCING WIFI IP ADDRESS TO MOBILE APP...
echo ---------------------------------------------------
powershell -NoProfile -ExecutionPolicy Bypass -Command ^
  "$ip = (Get-NetIPConfiguration | Where-Object { $_.IPv4DefaultGateway -ne $null -and $_.NetAdapter.Status -eq 'Up' } | Select-Object -First 1).IPv4Address.IPAddress;" ^
  "if (-not $ip) { $ip = (Test-Connection -ComputerName (hostname) -Count 1).IPV4Address.IPAddressToString };" ^
  "if ($ip) {" ^
  "  Write-Host '  [+] Current Wi-Fi IP detected: ' $ip -ForegroundColor Green;" ^
  "  $f='C:\xampp\htdocs\plant_app\plant_app_flutter\lib\config.dart';" ^
  "  $txt=Get-Content $f -Raw;" ^
  "  $txt=[regex]::Replace($txt,'static const String baseUrl = \".*?\";',('static const String baseUrl = \"http://' + $ip + '/plant_app/api\";'));" ^
  "  Set-Content -Path $f -Value $txt -Encoding UTF8;" ^
  "  Write-Host '  [+] Successfully locked app to your current network!' -ForegroundColor Green;" ^
  "} else { Write-Host '  [!] Could not detect IP automatically. Connect to Wi-Fi.' -ForegroundColor Red }"

echo.
echo 4. RUNNING APP...
echo    - If you see a list of devices, TYPE THE NUMBER (e.g. 1) and PRESS ENTER.
echo    - If you want to use the Emulator, OPEN IT NOW before continuing.
echo.
echo    Waiting for Flutter...
echo ---------------------------------------------------

d:\src\flutter\bin\flutter run --release

echo.
echo ---------------------------------------------------
echo App has exited.
echo ---------------------------------------------------
pause
