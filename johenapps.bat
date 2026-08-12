@echo off
title Johen Application
cd /d "%~dp0"

echo ========================================
echo   Johen Application
echo ========================================
echo.
echo   Semua services berjalan via composer run dev
echo   (server, queue, vite, absensi)
echo.
echo   Tekan Ctrl+C untuk menghentikan semua service.
echo ========================================
echo.

start "" powershell -NoProfile -WindowStyle Hidden -Command "Start-Process 'http://localhost:8000'"

composer run dev

echo.
echo   Semua service berhenti.
echo.

taskkill /f /im php.exe 2>nul
taskkill /f /im node.exe 2>nul
pause
