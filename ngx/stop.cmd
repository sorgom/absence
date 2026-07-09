@echo off
for %%i in (nginx php-cgi) do taskkill /F /IM %%i.exe >NUL 2>&1


