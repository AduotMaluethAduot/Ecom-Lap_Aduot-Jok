# Taste of Africa - E-Commerce Platform

A comprehensive e-commerce platform for selling authentic African cuisine, featuring multi-role user management (Admin, Restaurant Owner, and Customer), product management, order processing, and analytics.

## 📋 Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Project Structure](#project-structure)
- [User Roles](#user-roles)
- [Usage](#usage)
- [Security Features](#security-features)
- [File Structure](#file-structure)

## ✨ Features

### Customer Features
- User registration and authentication
- Browse products and menu items
- Search and filter products
- Add items to cart and favorites
- Place orders
- View order history
- Customer dashboard

### Restaurant Owner Features
- Restaurant owner dashboard
- Menu management (add, edit, delete dishes)
- Order management (view, accept, decline orders)
- Analytics and reports
- Restaurant settings

### Admin Features
- Complete admin dashboard
- User management
- Product management (categories, brands, products)
- Order management across all restaurants
- Analytics and reporting
- System settings
- Database backup and restore
- Role management

## 🛠 Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **Frameworks/Libraries**:
  - Bootstrap 5.3.0
  - Font Awesome 6.4.0
  - SweetAlert2
  - jQuery 3.6.0
- **Architecture**: MVC-like structure with PDO for database operations

## 📦 Prerequisites

- XAMPP (or similar PHP/MySQL stack)
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.4+
- Apache web server
- Modern web browser

## 🚀 Installation

1. **Clone or download the repository**
   ```bash
   git clone <repository-url>
   cd Ecom-Lap_Aduot-Jok
   ```

2. **Place the project in your web server directory**
   - For XAMPP: `C:\xampp\htdocs\Ecom-Lap_Aduot-Jok`
   - For WAMP: `C:\wamp64\www\Ecom-Lap_Aduot-Jok`
   - For LAMP: `/var/www/html/Ecom-Lap_Aduot-Jok`

3. **Start Apache and MySQL services** in XAMPP Control Panel

4. **Access the application**
   ```
   http://localhost/Ecom-Lap_Aduot-Jok/
   ```

## ⚙️ Configuration

### Database Configuration

Edit `config/config.php` to set your database credentials:

```php
// For local development (XAMPP)
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_2025A_aduot_jok');
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty for XAMPP default

// For production
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_2025A_aduot_jok');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

The system automatically detects the environment (local vs production) based on the hostname.

### Application Configuration

Key settings in `config/config.php`:

```php
define('APP_NAME', 'Taste of Africa');
define('APP_VERSION', '1.0.0');
define('SESSION_LIFETIME', 3600); // 1 hour
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
```

## 🗄️ Database Setup

1. **Open phpMyAdmin**
   ```
   http://localhost/phpmyadmin
   ```

2. **Import the database**
   - Click on "Import" tab
   - Choose the file: `database/dbforlab.sql`
   - Click "Go" to import

   **OR** run via command line:
   ```bash
   mysql -u root -p < database/dbforlab.sql
   ```

3. **Verify the database was created**
   - Database name: `ecommerce_2025A_aduot_jok`
   - Tables: `categories`, `brands`, `products`, `customer`, `cart`, `orders`, etc.

## 📁 Project Structure

```
Ecom-Lap_Aduot-Jok/
│
├── actions/              # AJAX action handlers
│   ├── login_customer_action.php
│   ├── register_user_action.php
│   ├── add_product_action.php
│   └── ...
│
├── admin/                # Admin dashboard pages
│   ├── dashboard.php
│   ├── product.php
│   ├── orders.php
│   ├── users.php
│   └── ...
│
├── classes/              # PHP classes
│   ├── product_class.php
│   ├── customer_class.php
│   ├── category_class.php
│   └── brand_class.php
│
├── config/              # Configuration files
│   └── config.php
│
├── controllers/         # Controller files
│   ├── product_controller.php
│   ├── customer_controller.php
│   └── ...
│
├── customer/            # Customer & Owner dashboards
│   ├── customer_dashboard.php
│   ├── owner_dashboard.php
│   ├── menu.php
│   ├── my_orders.php
│   └── ...
│
├── database/            # Database files
│   ├── database.php    # PDO connection
│   └── dbforlab.sql    # Database schema
│
├── login/               # Authentication pages
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── public/              # Public assets
│   ├── css/
│   │   ├── index.css
│   │   ├── login.css
│   │   └── admin.css
│   └── js/
│       ├── login.js
│       ├── product.js
│       └── ...
│
├── src/                 # Core source files
│   └── settings/
│       └── core.php    # Session & authentication
│
├── uploads/             # Uploaded files
│   ├── products/
│   └── receipts/
│
├── views/               # Product view pages
│   ├── all_product.php
│   ├── single_product.php
│   └── product_search_result.php
│
└── index.php            # Home page
```

## 👥 User Roles

### Admin (`admin`)
- Full system access
- Manage all products, categories, brands
- Manage all users and orders
- View system analytics
- Access admin dashboard

### Restaurant Owner (`owner` or `restaurant_owner`)
- Manage their own restaurant menu
- View and manage their orders
- Access restaurant analytics
- Restaurant settings management
- Access owner dashboard

### Customer (`customer`)
- Browse products and menu
- Place orders
- View order history
- Manage favorites
- Access customer dashboard

## 📖 Usage

### For Customers

1. **Register/Login**
   - Navigate to `login/register.php` to create an account
   - Or login at `login/login.php`

2. **Browse Products**
   - Visit the home page to see featured products
   - Use `views/all_product.php` to see all products
   - Search and filter products

3. **Place Orders**
   - Add items to cart
   - Proceed to checkout
   - View orders in customer dashboard

### For Restaurant Owners

1. **Login** with a restaurant owner account
2. **Access Owner Dashboard** (`customer/owner_dashboard.php`)
3. **Manage Menu** (`customer/owner_menu.php`)
   - Add new dishes
   - Edit existing items
   - Update prices and descriptions
4. **Manage Orders** (`customer/owner_orders.php`)
   - View incoming orders
   - Accept/decline orders
   - Update order status
5. **View Analytics** (`customer/owner_analytics.php`)

### For Administrators

1. **Login** with admin credentials
2. **Access Admin Dashboard** (`admin/dashboard.php`)
3. **Manage Products** (`admin/product.php`)
   - Add/edit/delete products
   - Manage categories and brands
4. **Manage Orders** (`admin/orders.php`)
5. **Manage Users** (`admin/users.php`)
6. **View Reports** (`admin/reports.php`)

## 🔒 Security Features

- **Session Management**: Secure session handling with regeneration
- **Password Hashing**: Passwords are hashed using PHP's password functions
- **SQL Injection Protection**: PDO with prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Session-based token validation (where implemented)
- **Role-Based Access Control**: Proper role checking on all protected pages
- **Secure File Uploads**: File type and size validation
- **Session Timeout**: Automatic session expiration after inactivity

## 📝 Notes

- The system automatically detects local vs production environment
- Default database name: `ecommerce_2025A_aduot_jok`
- Upload directories must be writable (chmod 755 for Linux/Mac)
- Ensure PHP extensions: `pdo`, `pdo_mysql`, `gd`, `mbstring` are enabled

## 🐛 Troubleshooting

### Database Connection Issues
- Verify MySQL is running in XAMPP
- Check database credentials in `config/config.php`
- Ensure database exists and is imported correctly

### Session Issues
- Check PHP session configuration
- Ensure `session.save_path` is writable
- Clear browser cookies if experiencing login issues

### File Upload Issues
- Check `uploads/` directory permissions
- Verify `UPLOAD_MAX_SIZE` in `config.php`
- Check PHP `upload_max_filesize` in `php.ini`

## 📄 License

This project is part of a lab assignment (Ecom-Lap_Aduot-Jok).

## 👤 Author

**Aduot Jok**

---

**Note**: This is an educational project. For production use, additional security measures and optimizations are recommended.

