@echo off
setlocal

set "TASK_NAME=FloraAI_Backend_AutoStart"

echo Removing scheduled task: %TASK_NAME%
schtasks /Delete /TN "%TASK_NAME%" /F >nul 2>nul

if %errorlevel%==0 (
    echo [OK] Auto-start task removed.
) else (
    echo [INFO] Task not found or already removed.
)

pause
