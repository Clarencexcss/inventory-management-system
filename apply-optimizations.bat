@echo off
echo ========================================
echo ButcherPro Performance Optimization Script
echo ========================================
echo.

echo [1/3] Optimizing Laravel application...
cd /d "%~dp0"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan butcherpro:optimize --cache
echo Laravel optimization completed.
echo.

echo [2/3] Please manually apply PHP optimizations:
echo    - Copy php-optimization.ini settings to your php.ini file
echo    - Key optimizations:
echo        * memory_limit = 1024M
echo        * opcache.enable = 1
echo        * opcache.memory_consumption = 256
echo        * realpath_cache_size = 4096K
echo.

echo [3/3] Please manually apply MySQL optimizations:
echo    - Copy mysql-optimization.cnf settings to your MySQL configuration file
echo    - Key optimizations:
echo        * innodb_buffer_pool_size = 512M
echo        * query_cache_size = 128M
echo        * key_buffer_size = 256M
echo        * max_connections = 150
echo.

echo ========================================
echo Optimization completed!
echo ========================================
echo.
echo Next steps:
echo 1. Restart your web server (Apache/Nginx)
echo 2. Test your application performance
echo.
pause