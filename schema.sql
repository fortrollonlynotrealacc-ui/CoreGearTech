-- Database schema for Project in WST
-- Import into MySQL/MariaDB before running the PHP app.

-- Select your existing database in phpMyAdmin before importing this file.

CREATE TABLE IF NOT EXISTS `cgt_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fname` VARCHAR(100) NOT NULL,
  `mname` VARCHAR(100) DEFAULT NULL,
  `lname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `age` INT UNSIGNED NOT NULL,
  `phonenumber` VARCHAR(20) NOT NULL,
  `address` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'active',
  `otp` VARCHAR(10) DEFAULT NULL,
  `otp_expiry` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cgt_accounts_email` (`email`),
  UNIQUE KEY `uniq_cgt_accounts_phone` (`phonenumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cgt_products` (
  `product_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(255) NOT NULL,
  `product_desc` TEXT NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `product_category` VARCHAR(100) NOT NULL,
  `product_specs` TEXT DEFAULT NULL,
  `product_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_id`),
  KEY `idx_cgt_products_category` (`product_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_cart_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `product_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `added_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_cart_items_user_product` (`user_id`, `product_id`),
  KEY `idx_user_cart_items_user_id` (`user_id`),
  KEY `idx_user_cart_items_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cgt_user_purchase` (
  `purchase_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `purchase_date` DATETIME NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` VARCHAR(50) NOT NULL,
  `account_number` VARCHAR(100) DEFAULT NULL,
  `order_status` VARCHAR(30) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`purchase_id`),
  KEY `idx_cgt_user_purchase_user_id` (`user_id`),
  KEY `idx_cgt_user_purchase_product_id` (`product_id`),
  KEY `idx_cgt_user_purchase_status` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cgt_guests` (
  `guest_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guest_name` VARCHAR(255) NOT NULL,
  `guest_email` VARCHAR(191) NOT NULL,
  `guest_address` VARCHAR(255) NOT NULL,
  `guest_number` VARCHAR(20) NOT NULL,
  `paypal_accountnumber` VARCHAR(191) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guest_id`),
  KEY `idx_cgt_guests_email` (`guest_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cgt_purchases` (
  `purchase_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guest_id` INT UNSIGNED NOT NULL,
  `purchase_date` DATETIME NOT NULL,
  PRIMARY KEY (`purchase_id`),
  KEY `idx_cgt_purchases_guest_id` (`guest_id`),
  KEY `idx_cgt_purchases_purchase_date` (`purchase_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cgt_guest_purchases` (
  `purchase_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `guest_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
  `purchase_date` DATETIME NOT NULL,
  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `order_status` VARCHAR(30) NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`purchase_id`),
  KEY `idx_cgt_guest_purchases_guest_id` (`guest_id`),
  KEY `idx_cgt_guest_purchases_product_id` (`product_id`),
  KEY `idx_cgt_guest_purchases_purchase_date` (`purchase_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contact_us` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contact_us_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `message` TEXT NOT NULL,
  `message_type` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_user_id` (`user_id`),
  KEY `idx_messages_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `feedback` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `feedback_message` TEXT NOT NULL,
  `admin_reply` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_feedback_product_id` (`product_id`),
  KEY `idx_feedback_user_id` (`user_id`),
  KEY `idx_feedback_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE OR REPLACE VIEW `users` AS
SELECT
  `id`,
  CONCAT_WS(' ', `fname`, `mname`, `lname`) AS `name`,
  `email`,
  `status`
FROM `cgt_accounts`;

CREATE OR REPLACE VIEW `cgt_user_purchases` AS
SELECT
  `purchase_id`,
  `user_id`,
  `product_id`,
  `quantity`,
  `purchase_date`,
  `price`,
  `payment_method`,
  `account_number`,
  `order_status`
FROM `cgt_user_purchase`;