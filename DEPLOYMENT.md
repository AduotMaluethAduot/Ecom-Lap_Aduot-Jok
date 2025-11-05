# 🚀 Deployment Guide - Taste of Africa E-commerce Platform

## 📋 Server Requirements

### **Minimum Requirements:**
- **PHP**: 7.4+ or 8.0+
- **MySQL**: 5.7+ or 8.0+
- **Web Server**: Apache 2.4+ or Nginx
- **Storage**: 100MB minimum
- **Memory**: 512MB RAM minimum

### **Recommended:**
- **PHP**: 8.1+
- **MySQL**: 8.0+
- **Apache**: 2.4+ with mod_rewrite enabled
- **Storage**: 1GB
- **Memory**: 1GB RAM

## 🎯 Deployment Methods

### **Method 1: Git Clone (Recommended)**

```bash
# SSH into your server
ssh username@your-server.com

# Navigate to web directory
cd /var/www/html  # or /public_html for shared hosting

# Clone the repository
git clone https://github.com/AduotMaluethAduot/Ecom-Lap_Aduot-Jok.git

# Set proper permissions
chmod -R 755 Ecom-Lap_Aduot-Jok/
chmod -R 777 Ecom-Lap_Aduot-Jok/uploads/
```

### **Method 2: cPanel File Manager**

1. **Download ZIP** from GitHub:
   - Go to: https://github.com/AduotMaluethAduot/Ecom-Lap_Aduot-Jok
   - Click "Code" → "Download ZIP"

2. **Upload to Server**:
   - Login to cPanel
   - Open File Manager
   - Navigate to `public_html`
   - Upload the ZIP file
   - Extract the ZIP file
   - Rename folder to your desired domain name

### **Method 3: FTP/SFTP**

1. **Download ZIP** from GitHub
2. **Extract locally**
3. **Upload via FTP/SFTP** to your web directory

## 🗄️ Database Setup

### **Step 1: Create Database**

```sql
-- Create database
CREATE DATABASE ecommerce_2025A_aduot_jok 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create user (if needed)
CREATE USER 'aduot.jok'@'localhost' IDENTIFIED BY 'Aduot12';

-- Grant privileges
GRANT ALL PRIVILEGES ON ecommerce_2025A_aduot_jok.* TO 'aduot.jok'@'localhost';
FLUSH PRIVILEGES;
```

### **Step 2: Import Database Schema**

```bash
# Via command line
mysql -u aduot.jok -p ecommerce_2025A_aduot_jok < database/dbforlab.sql

# Via phpMyAdmin
# 1. Login to phpMyAdmin
# 2. Select database: ecommerce_2025A_aduot_jok
# 3. Go to Import tab
# 4. Upload file: database/dbforlab.sql
# 5. Click "Go"
```

## ⚙️ Configuration

### **Database Configuration**
The application automatically detects the environment:
- **Local Development**: Uses `root` (no password)
- **Server**: Uses `aduot.jok` with password `Aduot12`

### **File Permissions**
```bash
# Set proper permissions
chmod 644 *.php
chmod 755 uploads/
chmod 777 uploads/products/
chmod 777 uploads/receipts/
```

### **Apache Configuration (.htaccess)**
Create `.htaccess` in root directory:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

## 🔧 Post-Deployment Setup

### **1. Test Database Connection**
Visit: `https://yourdomain.com/index.php`

### **2. Test Login System**
- **Admin**: admin@tasteofafrica.com / password123
- **Customer**: john@example.com / password123

### **3. Configure Email (Optional)**
Update `config/config.php` with your SMTP settings:

```php
define('SMTP_HOST', 'your-smtp-server.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@domain.com');
define('SMTP_PASS', 'your-email-password');
```

### **4. SSL Certificate**
Ensure your domain has SSL certificate for secure transactions.

## 🚨 Troubleshooting

### **Common Issues:**

1. **Database Connection Failed**
   - Check database credentials in `config/config.php`
   - Verify database exists and user has permissions

2. **File Upload Issues**
   - Check `uploads/` directory permissions (777)
   - Verify PHP upload limits in php.ini

3. **404 Errors**
   - Enable mod_rewrite in Apache
   - Check .htaccess file exists

4. **Session Issues**
   - Check PHP session configuration
   - Verify session directory is writable

## 📞 Support

For deployment issues:
- Check server error logs
- Verify PHP and MySQL versions
- Test with sample data first

## 🎯 Production Checklist

- [ ] Database created and imported
- [ ] File permissions set correctly
- [ ] SSL certificate installed
- [ ] Email configuration tested
- [ ] Admin account created
- [ ] Sample products added
- [ ] Payment gateway configured (if applicable)
- [ ] Backup system in place

---

**Repository**: https://github.com/AduotMaluethAduot/Ecom-Lap_Aduot-Jok
**Documentation**: This file
