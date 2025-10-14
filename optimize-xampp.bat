@echo off
echo ========================================
echo ButcherPro XAMPP Performance Optimization
echo ========================================
echo.

echo [1/6] Stopping XAMPP services...
net stop "Apache2.4" 2>nul
net stop "mysql" 2>nul
echo Services stopped.
echo.

echo [2/6] Optimizing Laravel application...
cd /d "%~dp0"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan butcherpro:optimize --all
echo Laravel optimization completed.
echo.

echo [3/6] Backing up current configurations...
if not exist "backup" mkdir backup
copy "C:\xampp\php\php.ini" "backup\php.ini.backup" 2>nul
copy "C:\xampp\mysql\bin\my.ini" "backup\my.ini.backup" 2>nul
echo Configurations backed up.
echo.

echo [4/6] Applying PHP optimizations...
echo Please manually copy the contents of php-optimization.ini to C:\xampp\php\php.ini
echo Key optimizations:
echo - memory_limit = 1024M
echo - opcache.enable = 1
echo - opcache.memory_consumption = 256
echo - realpath_cache_size = 4096K
echo.

echo [5/6] Applying MySQL optimizations...
echo Please manually copy the contents of mysql-optimization.cnf to C:\xampp\mysql\bin\my.ini
echo Key optimizations:
echo - innodb_buffer_pool_size = 512M
echo - query_cache_size = 128M
echo - key_buffer_size = 256M
echo - max_connections = 150
echo.

echo [6/6] Starting XAMPP services...
net start "Apache2.4" 2>nul
net start "mysql" 2>nul
echo Services started.
echo.

echo ========================================
echo Optimization completed!
echo ========================================
echo.
echo Next steps:
echo 1. Copy php-optimization.ini settings to C:\xampp\php\php.ini
echo 2. Copy mysql-optimization.cnf settings to C:\xampp\mysql\bin\my.ini
echo 3. Restart XAMPP services
echo 4. Test your application performance
echo.
echo Performance monitoring available at:
echo - GET /api/performance/metrics
echo - GET /api/performance/diagnostics
echo - POST /api/performance/optimize
echo.
pause
