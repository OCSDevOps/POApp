@echo off
setlocal

cd /d "%~dp0"

where php >nul 2>nul
if errorlevel 1 (
    echo PHP was not found on PATH.
    echo Install PHP or add it to PATH, then try again.
    pause
    exit /b 1
)

echo Starting Laravel backend...
echo URL: http://127.0.0.1:9000
echo.

if exist "%~dp0php.ini" (
    php -c "%~dp0php.ini" artisan serve --host=127.0.0.1 --port=9000
) else (
    php artisan serve --host=127.0.0.1 --port=9000
)

endlocal
