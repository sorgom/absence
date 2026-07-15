@echo off
for %%i in (nginx.exe php-cgi.exe) do (
    echo.
    echo ===== %%i
    tasklist /FI "IMAGENAME eq %%i"
)
