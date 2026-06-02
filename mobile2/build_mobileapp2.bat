@echo off
title Build Flutter Web -> Laravel Public/MobileApp2

echo =====================================
echo BUILD FLUTTER WEB
echo =====================================

call flutter clean
if errorlevel 1 goto error

call flutter pub get
if errorlevel 1 goto error

call flutter build web --release
if errorlevel 1 goto error

echo.
echo =====================================
echo COPY TO LARAVEL PUBLIC
echo =====================================

if exist "..\public\MobileApp2" (
    rmdir /s /q "..\public\MobileApp2"
)

xcopy "build\web" "..\public\MobileApp2" /E /I /Y >nul

echo.
echo =====================================
echo BUILD SUCCESS
echo Output:
echo ..\public\MobileApp2
echo =====================================
pause
exit /b 0

:error
echo.
echo =====================================
echo BUILD FAILED
echo =====================================
pause
exit /b 1