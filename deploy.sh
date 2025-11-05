#!/bin/bash

# 🚀 Taste of Africa E-commerce Platform - Deployment Script
# This script automates the deployment process

echo "🚀 Starting deployment of Taste of Africa E-commerce Platform..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_warning "Running as root. Consider using a non-root user for security."
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
print_status "PHP Version: $PHP_VERSION"

if [[ $(echo "$PHP_VERSION 7.4" | awk '{print ($1 >= $2)}') == 1 ]]; then
    print_status "✅ PHP version is compatible"
else
    print_error "❌ PHP 7.4+ required. Current version: $PHP_VERSION"
    exit 1
fi

# Check MySQL/MariaDB
if command -v mysql &> /dev/null; then
    print_status "✅ MySQL/MariaDB is available"
else
    print_error "❌ MySQL/MariaDB not found"
    exit 1
fi

# Set deployment directory
DEPLOY_DIR="/var/www/html"
if [ ! -d "$DEPLOY_DIR" ]; then
    DEPLOY_DIR="$HOME/public_html"
fi

if [ ! -d "$DEPLOY_DIR" ]; then
    print_error "❌ Web directory not found. Please specify DEPLOY_DIR"
    exit 1
fi

print_status "Deployment directory: $DEPLOY_DIR"

# Clone or update repository
REPO_URL="https://github.com/AduotMaluethAduot/Ecom-Lap_Aduot-Jok.git"
PROJECT_DIR="$DEPLOY_DIR/Ecom-Lap_Aduot-Jok"

if [ -d "$PROJECT_DIR" ]; then
    print_status "Updating existing repository..."
    cd "$PROJECT_DIR"
    git pull origin main
else
    print_status "Cloning repository..."
    cd "$DEPLOY_DIR"
    git clone "$REPO_URL"
fi

# Set proper permissions
print_status "Setting file permissions..."
chmod -R 755 "$PROJECT_DIR"
chmod -R 777 "$PROJECT_DIR/uploads"
chmod -R 777 "$PROJECT_DIR/uploads/products"
chmod -R 777 "$PROJECT_DIR/uploads/receipts"

# Create .htaccess if it doesn't exist
HTACCESS_FILE="$PROJECT_DIR/.htaccess"
if [ ! -f "$HTACCESS_FILE" ]; then
    print_status "Creating .htaccess file..."
    cat > "$HTACCESS_FILE" << 'EOF'
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"

# Prevent access to sensitive files
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

<Files "*.md">
    Order allow,deny
    Deny from all
</Files>
EOF
fi

# Database setup prompt
print_warning "Database setup required:"
echo "1. Create database: ecommerce_2025A_aduot_jok"
echo "2. Create user: aduot.jok with password: Aduot12"
echo "3. Import database/dbforlab.sql"
echo ""
echo "Run these commands:"
echo "mysql -u root -p -e \"CREATE DATABASE ecommerce_2025A_aduot_jok CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\""
echo "mysql -u root -p -e \"CREATE USER 'aduot.jok'@'localhost' IDENTIFIED BY 'Aduot12';\""
echo "mysql -u root -p -e \"GRANT ALL PRIVILEGES ON ecommerce_2025A_aduot_jok.* TO 'aduot.jok'@'localhost';\""
echo "mysql -u root -p -e \"FLUSH PRIVILEGES;\""
echo "mysql -u aduot.jok -p ecommerce_2025A_aduot_jok < $PROJECT_DIR/database/dbforlab.sql"

print_status "✅ Deployment completed!"
print_status "🌐 Access your application at: http://your-domain.com/Ecom-Lap_Aduot-Jok/"
print_status "👤 Admin login: admin@tasteofafrica.com / password123"
print_status "👤 Customer login: john@example.com / password123"

echo ""
print_warning "Next steps:"
echo "1. Complete database setup"
echo "2. Test the application"
echo "3. Configure SSL certificate"
echo "4. Set up email configuration"
echo "5. Add your domain to the project directory"
