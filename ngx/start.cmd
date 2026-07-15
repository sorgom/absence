@echo off
SETLOCAL

cd /d %~dp0
set myDir=%cd%
cd ..
set topDir=%cd%
set logDir=%cd%\logs
set errlog=%logDir%\error.log

for %%d in (%logDir% %topDir%\temp) do if not exist %%d mkdir %%d

cd %myDir%
call stop.cmd

start /B php-cgi.exe -b 127.0.0.1:9000
start /B nginx.exe -e %logDir%\error.log -c %myDir%\nginx.conf -p %topDir%

timeout /t 1 /nobreak >nul
call state.cmd

ENDLOCAL
