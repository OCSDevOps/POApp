@echo off
setlocal

cd /d "%~dp0"

where npm >nul 2>nul
if errorlevel 1 (
    echo npm was not found on PATH.
    echo Install Node.js or add it to PATH, then try again.
    pause
    exit /b 1
)

echo Starting Vite frontend...
echo Vite dev server: http://127.0.0.1:5173
echo Laravel app:     http://127.0.0.1:9000
echo.

call npm run dev -- --host 127.0.0.1 --port 5173

endlocal
