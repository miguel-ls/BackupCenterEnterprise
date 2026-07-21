@echo off
setlocal
set LOGFILE=%~dp0worker.log
echo [%DATE% %TIME%] iniciando worker de prueba >> "%LOGFILE%"
echo BackupCenter worker de prueba listo. >> "%LOGFILE%"
pause
