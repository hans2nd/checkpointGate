@echo off
cd /d "d:\laragon\www\checkpointGate"

echo ============================================
echo   Checkpoint GIIC - VPS Sync
echo   %date% %time%
echo ============================================
echo.

php artisan sync:checkpoints

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ============================================
    echo   [ERROR] Sync gagal! Error code: %ERRORLEVEL%
    echo ============================================
)

echo.
pause
