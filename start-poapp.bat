@echo off
setlocal

cd /d "%~dp0"

echo Launching POApp backend and frontend...
echo Backend:  http://127.0.0.1:9000
echo Frontend: http://127.0.0.1:5173
echo.

start "POApp Backend" cmd /k "\"%~dp0start-backend.bat\""
start "POApp Frontend" cmd /k "\"%~dp0start-frontend.bat\""

echo Both windows were opened.
echo Close each window or press Ctrl+C inside it when you want to stop the servers.

endlocal
