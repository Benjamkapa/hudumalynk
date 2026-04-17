@echo off
title HudumaLynk - Frontend + Backend Servers
echo Starting HudumaLynk servers...
echo Frontend: http://localhost:8080
echo Backend/Admin: http://localhost:8081
echo Press Ctrl+C to stop both.

start "Frontend" /B php yii serve --docroot=frontend/web --port=8080
start "Backend" /B php yii serve --docroot=backend/web --port=8081

echo Servers started!
:loop
timeout /t 1 /nobreak >nul
goto loop

