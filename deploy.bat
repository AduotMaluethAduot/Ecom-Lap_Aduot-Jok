@echo off
REM 🚀 Taste of Africa E-commerce Platform - Windows Deployment Script

echo 🚀 Starting deployment of Taste of Africa E-commerce Platform...

REM Check if Git is available
git --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Git not found. Please install Git first.
    pause
    exit /b 1
)

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP not found. Please install PHP first.
    pause
    exit /b 1
)

echo ✅ Git and PHP are available

REM Set deployment directory (adjust as needed)
set DEPLOY_DIR=C:\inetpub\wwwroot
if not exist "%DEPLOY_DIR%" (
    set DEPLOY_DIR=%USERPROFILE%\public_html
)

if not exist "%DEPLOY_DIR%" (
    echo ❌ Web directory not found. Please specify DEPLOY_DIR
    pause
    exit /b 1
)

echo Deployment directory: %DEPLOY_DIR%

REM Clone or update repository
set REPO_URL=https://github.com/AduotMaluethAduot/Ecom-Lap_Aduot-Jok.git
set PROJECT_DIR=%DEPLOY_DIR%\Ecom-Lap_Aduot-Jok

if exist "%PROJECT_DIR%" (
    echo Updating existing repository...
    cd /d "%PROJECT_DIR%"
    git pull origin main
) else (
    echo Cloning repository...
    cd /d "%DEPLOY_DIR%"
    git clone %REPO_URL%
)

REM Set proper permissions (Windows)
echo Setting file permissions...
icacls "%PROJECT_DIR%" /grant Everyone:F /T >nul 2>&1

REM Create .htaccess if it doesn't exist
set HTACCESS_FILE=%PROJECT_DIR%\.htaccess
if not exist "%HTACCESS_FILE%" (
    echo Creating .htaccess file...
    (
        echo RewriteEngine On
        echo RewriteCond %%{REQUEST_FILENAME} !-f
        echo RewriteCond %%{REQUEST_FILENAME} !-d
        echo RewriteRule ^^(.*^)$ index.php [QSA,L]
        echo.
        echo # Security headers
        echo Header always set X-Content-Type-Options nosniff
        echo Header always set X-Frame-Options DENY
        echo Header always set X-XSS-Protection "1; mode=block"
        echo.
        echo # Prevent access to sensitive files
        echo ^<Files "*.sql"^>
        echo     Order allow,deny
        echo     Deny from all
        echo ^</Files^>
        echo.
        echo ^<Files "*.md"^>
        echo     Order allow,deny
        echo     Deny from all
        echo ^</Files^>
    ) > "%HTACCESS_FILE%"
)

echo.
echo ⚠️  Database setup required:
echo 1. Create database: ecommerce_2025A_aduot_jok
echo 2. Create user: aduot.jok with password: Aduot12
echo 3. Import database/dbforlab.sql
echo.
echo Run these commands in MySQL:
echo CREATE DATABASE ecommerce_2025A_aduot_jok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo CREATE USER 'aduot.jok'@'localhost' IDENTIFIED BY 'Aduot12';
echo GRANT ALL PRIVILEGES ON ecommerce_2025A_aduot_jok.* TO 'aduot.jok'@'localhost';
echo FLUSH PRIVILEGES;
echo.
echo Then import: mysql -u aduot.jok -p ecommerce_2025A_aduot_jok ^< %PROJECT_DIR%\database\dbforlab.sql

echo.
echo ✅ Deployment completed!
echo 🌐 Access your application at: http://localhost/Ecom-Lap_Aduot-Jok/
echo 👤 Admin login: admin@tasteofafrica.com / password123
echo 👤 Customer login: john@example.com / password123

echo.
echo ⚠️  Next steps:
echo 1. Complete database setup
echo 2. Test the application
echo 3. Configure SSL certificate
echo 4. Set up email configuration
echo 5. Add your domain to the project directory

pause
