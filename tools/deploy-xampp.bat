@echo off
setlocal

for %%I in ("%~dp0..\advanced") do set "PROJECT_DIR=%%~fI"
set "XAMPP_ROOT=%~1"
if not defined XAMPP_ROOT set "XAMPP_ROOT=C:\xampp"

set "PHP_EXE=%XAMPP_ROOT%\php\php.exe"
set "MYSQL_EXE=%XAMPP_ROOT%\mysql\bin\mysql.exe"
set "DB_NAME=worldcup"
set "MYSQL_USER=root"

if not exist "%PROJECT_DIR%\yii" (
    echo ERROR: Yii project not found: "%PROJECT_DIR%"
    exit /b 1
)
if not exist "%PHP_EXE%" (
    echo ERROR: XAMPP PHP not found: "%PHP_EXE%"
    echo Usage: "%~nx0" "D:\xampp"
    exit /b 1
)
if not exist "%MYSQL_EXE%" (
    echo ERROR: XAMPP MySQL client not found: "%MYSQL_EXE%"
    exit /b 1
)

set "PATH=%XAMPP_ROOT%\php;%PATH%"
"%PHP_EXE%" -r "exit(version_compare(PHP_VERSION, '8.2.12', '<') ? 1 : 0);"
if errorlevel 1 (
    echo ERROR: PHP 8.2.12 or later is required by composer.json.
    "%PHP_EXE%" -v
    exit /b 1
)
"%PHP_EXE%" -r "exit(extension_loaded('pdo_mysql') ? 0 : 1);"
if errorlevel 1 (
    echo ERROR: Enable the pdo_mysql extension in "%XAMPP_ROOT%\php\php.ini".
    exit /b 1
)

where composer >nul 2>nul
if errorlevel 1 (
    echo ERROR: Composer was not found on PATH. Install Composer, then rerun this script.
    exit /b 1
)
pushd "%PROJECT_DIR%"
if errorlevel 1 exit /b 1
"%PHP_EXE%" init --env=Development --overwrite=n
if errorlevel 1 (
    popd
    echo ERROR: Yii environment initialization failed.
    exit /b 1
)
composer install --no-interaction
if errorlevel 1 (
    popd
    echo ERROR: Composer dependency installation failed.
    exit /b 1
)
popd

echo Creating database "%DB_NAME%" if it does not exist...
set /p "MYSQL_USER=MySQL username [root]: "
if not defined MYSQL_USER set "MYSQL_USER=root"
"%MYSQL_EXE%" --protocol=TCP -h 127.0.0.1 -u "%MYSQL_USER%" -p -e "CREATE DATABASE IF NOT EXISTS `%DB_NAME%` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if errorlevel 1 (
    echo ERROR: Could not create the database. Check MySQL is running and verify the credentials.
    echo Create "%DB_NAME%" manually, update common/config/main-local.php, then run Yii migrations.
    exit /b 1
)

pushd "%PROJECT_DIR%"
if errorlevel 1 exit /b 1
"%PHP_EXE%" yii migrate --interactive=0
if errorlevel 1 (
    popd
    echo ERROR: Yii migrations failed. Check common/config/main-local.php and MySQL.
    exit /b 1
)
popd

echo Deployment initialization completed.
echo Configure Apache document roots for frontend\web and backend\web as described in docs\A\部署文档.md.
exit /b 0
