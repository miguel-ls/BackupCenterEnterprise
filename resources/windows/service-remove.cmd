@echo off
setlocal
set SERVICE_NAME=BackupCenterWorker
set SERVICE_EXE=%SystemRoot%\System32\sc.exe

"%SERVICE_EXE%" stop "%SERVICE_NAME%" >nul 2>&1
"%SERVICE_EXE%" delete "%SERVICE_NAME%" >nul 2>&1

echo Servicio eliminado: %SERVICE_NAME%
