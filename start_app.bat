@echo off
set "BASE_DIR=%~dp0"

:: 1. Force kill any stuck PHP processes first so the port is totally free (suppress errors)
taskkill /f /im php.exe >nul 2>&1

:: 2. Ensure we are in the 'www' app directory natively
cd /d "%BASE_DIR%"

:: 3. Start PHP using our perfectly configured standalone bundled PHP 
:: Since this bat file is now inside "www", the php executable is one folder backward ("..\php")
start /B "" "%BASE_DIR%xamp-php\php.exe" -S 127.0.0.1:8282 -t public server.php > "%BASE_DIR%artisan_serve.log" 2>&1

:: 4. Pause for exactly 4 seconds
ping 127.0.0.1 -n 5 > nul

:: 5. Open Edge in App Mode
start /WAIT "" msedge.exe --app="http://127.0.0.1:8282" --user-data-dir="%LOCALAPPDATA%\SensoryAssessment\EdgeUI"

:: 6. Cleanup PHP gracefully on exit
taskkill /f /im php.exe >nul 2>&1
exit
