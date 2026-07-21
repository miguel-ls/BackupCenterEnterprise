@echo off
setlocal
set SERVICE_NAME=BackupCenterWorker
set SERVICE_EXE=%SystemRoot%\System32\sc.exe
set SCRIPT_PATH=%~dp0worker-test.cmd

"%SERVICE_EXE%" delete "%SERVICE_NAME%" >nul 2>&1
"%SERVICE_EXE%" create "%SERVICE_NAME%" binPath= "cmd /c \"%SCRIPT_PATH%\"" start= auto >nul 2>&1
"%SERVICE_EXE%" start "%SERVICE_NAME%" >nul 2>&1

echo Servicio creado: %SERVICE_NAME%
