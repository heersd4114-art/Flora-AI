@echo off
setlocal

set "TASK_NAME=FloraAI_Backend_AutoStart"
set "TASK_CMD=C:\xampp\htdocs\plant_app\start_all_services.bat"

echo Creating scheduled task: %TASK_NAME%
schtasks /Create /TN "%TASK_NAME%" /TR "\"%TASK_CMD%\"" /SC ONLOGON /RL HIGHEST /F >nul 2>nul

if %errorlevel%==0 (
    echo [OK] Auto-start task created successfully.
    echo It will run at every Windows logon.
) else (
    echo [ERROR] Failed to create scheduled task.
    echo Run this file as Administrator.
)

pause
