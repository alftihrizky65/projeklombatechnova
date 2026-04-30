@echo off
title SignEdu 8-Bit MVP Subsystems
echo ==============================================
echo       SignEdu 8-Bit Multi-Server Launcher
echo ==============================================

:: Start Laravel Backend (Port 8000)
echo Starting Laravel Backend...
start cmd /k "cd backend && php artisan serve --port=8000"

:: Start Python FastAPI (Port 8001)
echo Starting AI Engine (FastAPI)...
start cmd /k "cd ai_engine && .\venv\Scripts\activate && python main.py"

:: Start Next.js Frontend (Port 3000)
echo Starting Next.js Frontend...
start cmd /k "cd frontend && npm run dev"

echo.
echo All Servers are starting!
echo Next.js: http://localhost:3000
echo Laravel API: http://127.0.0.1:8000
echo FastAPI WS: ws://127.0.0.1:8001/ws
echo.
pause
