@echo off
echo =======================================================
echo     Sensory Assessment Installer Preparation Script    
echo =======================================================
echo.

echo 1. Creating Database and Emptying Logs...
if not exist "database\database.sqlite" (
    echo. > "database\database.sqlite"
)
type NUL > storage\logs\laravel.log

echo.
echo 2. Clearing Caches and Old Files...
php artisan optimize:clear

echo.
echo 3. Optimizing Composer Dependencies...
:: Ensure you have composer installed locally
call composer dump-autoload --optimize

echo.
echo 4. Caching Laravel Framework...
php artisan optimize

echo.
echo 5. Caching Filament and Assets...
php artisan filament:cache-components
php artisan icons:cache
call npm run build

echo.
echo =======================================================
echo All optimizations complete! 
echo Your app is now ready to be packaged via Inno Setup.
echo =======================================================
pause
