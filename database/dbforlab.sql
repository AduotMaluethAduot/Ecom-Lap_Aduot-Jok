-- ------------------------------------------------------------
-- phpMyAdmin SQL Dump
-- Updated for MySQL in XAMPP Environment
-- Compatible with MySQL 8.0+ and MariaDB 10.4+
-- Project: Register_Lap2_Aduot-Jok
-- Authentication System with Session Management
-- ------------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

 /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- ------------------------------------------------------------
-- Database: `ecommerce_2025A_aduot_jok`
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `ecommerce_2025A_aduot_jok`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `ecommerce_2025A_aduot_jok`;

-- ------------------------------------------------------------
-- Table structure for `categories`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `brands`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `brands` (
  `brand_id` INT(11) NOT NULL AUTO_INCREMENT,
  `brand_name` VARCHAR(100) NOT NULL,
  `cat_id` INT(11) NOT NULL,
  PRIMARY KEY (`brand_id`),
  KEY `cat_id` (`cat_id`),
  CONSTRAINT `brands_ibfk_1` FOREIGN KEY (`cat_id`)
    REFERENCES `categories` (`cat_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `customer`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customer` (
  `customer_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_pass` VARCHAR(255) NOT NULL,
  `customer_country` VARCHAR(100) NOT NULL,
  `customer_city` VARCHAR(100) NOT NULL,
  `customer_contact` VARCHAR(20) NOT NULL,
  `customer_image` VARCHAR(255) DEFAULT NULL,
  `user_role` VARCHAR(20) NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `customer_email` (`customer_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `products`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_cat` INT(11) NOT NULL,
  `product_brand` INT(11) NOT NULL,
  `product_title` VARCHAR(200) NOT NULL,
  `product_price` DECIMAL(10,2) NOT NULL,
  `product_desc` TEXT,
  `product_image` VARCHAR(255) DEFAULT NULL,
  `product_keywords` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `product_cat` (`product_cat`),
  KEY `product_brand` (`product_brand`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_cat`)
    REFERENCES `categories` (`cat_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`product_brand`)
    REFERENCES `brands` (`brand_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `cart`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `p_id` INT(11) NOT NULL,
  `ip_add` VARCHAR(50) NOT NULL,
  `c_id` INT(11) DEFAULT NULL,
  `qty` INT(11) NOT NULL,
  KEY `p_id` (`p_id`),
  KEY `c_id` (`c_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`p_id`)
    REFERENCES `products` (`product_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`c_id`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `orders`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL,
  `invoice_no` VARCHAR(50) NOT NULL,
  `order_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `order_status` VARCHAR(100) NOT NULL DEFAULT 'pending',
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `delivery_address` TEXT DEFAULT NULL,
  `delivery_phone` VARCHAR(20) DEFAULT NULL,
  `special_instructions` TEXT DEFAULT NULL,
  `admin_notes` TEXT DEFAULT NULL,
  `approved_by` INT(11) DEFAULT NULL,
  `approved_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  KEY `customer_id` (`customer_id`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`approved_by`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `orderdetails`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orderdetails` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orderdetails_ibfk_1` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`order_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `orderdetails_ibfk_2` FOREIGN KEY (`product_id`)
    REFERENCES `products` (`product_id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table structure for `payment`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payment` (
  `pay_id` INT(11) NOT NULL AUTO_INCREMENT,
  `amt` DECIMAL(10,2) NOT NULL,
  `customer_id` INT(11) NOT NULL,
  `order_id` INT(11) NOT NULL,
  `currency` VARCHAR(10) NOT NULL DEFAULT 'USD',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'cash',
  `payment_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
  `payment_reference` VARCHAR(100) DEFAULT NULL,
  `receipt_image` VARCHAR(255) DEFAULT NULL,
  `admin_verified` TINYINT(1) DEFAULT 0,
  `verified_by` INT(11) DEFAULT NULL,
  `verified_at` TIMESTAMP NULL DEFAULT NULL,
  `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`pay_id`),
  KEY `customer_id` (`customer_id`),
  KEY `order_id` (`order_id`),
  KEY `verified_by` (`verified_by`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`customer_id`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`order_id`)
    REFERENCES `orders` (`order_id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_ibfk_3` FOREIGN KEY (`verified_by`)
    REFERENCES `customer` (`customer_id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Sample Data Inserts
-- ------------------------------------------------------------
INSERT INTO `categories` (`cat_name`) VALUES
('African Cuisine'),
('Beverages'),
('Desserts'),
('Appetizers');

INSERT INTO `brands` (`brand_name`, `cat_id`) VALUES
('Taste of Africa', 1),
('Authentic Flavors', 1),
('Traditional Recipes', 2);

INSERT INTO `customer`
(`customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`)
VALUES
('Admin User', 'admin@tasteofafrica.com', '$2y$10$8K1p/MQmTm5qPkHg5j1.Eu5RjHsVpV8M2Y.sGg7vE6aF9dF8Y2F8W', 'Ghana', 'Accra', '+233123456789', NULL, 'admin'),
('John Customer', 'john@example.com', '$2y$10$8K1p/MQmTm5qPkHg5j1.Eu5RjHsVpV8M2Y.sGg7vE6aF9dF8Y2F8W', 'Nigeria', 'Lagos', '+234987654321', NULL, 'customer'),
('Jane Smith', 'jane@example.com', '$2y$10$8K1p/MQmTm5qPkHg5j1.Eu5RjHsVpV8M2Y.sGg7vE6aF9dF8Y2F8W', 'Kenya', 'Nairobi', '+254567890123', NULL, 'customer');

INSERT INTO `products`
(`product_cat`, `product_brand`, `product_title`, `product_price`, `product_desc`, `product_image`, `product_keywords`)
VALUES
(1, 1, 'Jollof Rice Special', 15.99, 'Authentic West African Jollof rice with chicken and vegetables', 'uploads/products/jollof_rice.svg', 'jollof, rice, african, chicken'),
(1, 1, 'Injera with Doro Wat', 18.50, 'Traditional Ethiopian bread with spicy chicken stew', 'uploads/products/injera_doro.svg', 'injera, ethiopian, doro wat, spicy'),
(2, 2, 'Hibiscus Tea (Zobo)', 5.99, 'Refreshing Nigerian hibiscus tea with natural spices', 'uploads/products/hibiscus_tea.svg', 'hibiscus, tea, zobo, refreshing'),
(4, 3, 'Plantain Chips', 8.99, 'Crispy fried plantain chips seasoned with African spices', 'uploads/products/plantain_chips.svg', 'plantain, chips, snack, crispy');

COMMIT;

 /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
