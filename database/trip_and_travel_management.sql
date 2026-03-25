-- Trip and Travel Management (7trip)
-- Database schema + minimal seed data
-- Compatible with MySQL 5.7+/8.0+ and MariaDB 10.3+

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `trip_and_travel_management`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `trip_and_travel_management`;

-- Drop tables (optional reset)
-- Disable FK checks for clean re-create
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tbl_notifications`;
DROP TABLE IF EXISTS `tbl_rating`;
DROP TABLE IF EXISTS `tbl_booking`;
DROP TABLE IF EXISTS `tbl_assign`;
DROP TABLE IF EXISTS `tbl_vehicle`;
DROP TABLE IF EXISTS `tbl_routes`;
DROP TABLE IF EXISTS `tbl_location`;
DROP TABLE IF EXISTS `tbl_category`;
DROP TABLE IF EXISTS `tbl_country`;
DROP TABLE IF EXISTS `tbl_faq`;
DROP TABLE IF EXISTS `tbl_user`;
SET FOREIGN_KEY_CHECKS = 1;

-- Users
CREATE TABLE `tbl_user` (
  `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_name` VARCHAR(100) NOT NULL,
  `user_address` VARCHAR(255) NOT NULL,
  `user_age` INT NULL,
  `user_gender` VARCHAR(20) NULL,
  `user_phone` VARCHAR(30) NULL,
  `user_email` VARCHAR(150) NOT NULL,
  `user_status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  `user_password` VARCHAR(255) NOT NULL,
  `user_role` ENUM('admin','organizer','customer') NOT NULL DEFAULT 'customer',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_user_email` (`user_email`),
  KEY `idx_user_role` (`user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FAQ
CREATE TABLE `tbl_faq` (
  `faq_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` TEXT NOT NULL,
  `answer` TEXT NULL,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Country
CREATE TABLE `tbl_country` (
  `c_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_name` VARCHAR(150) NOT NULL,
  `c_description` TEXT NULL,
  `c_status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `uq_country_name` (`c_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Category
CREATE TABLE `tbl_category` (
  `cat_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(150) NOT NULL,
  `cat_description` TEXT NULL,
  PRIMARY KEY (`cat_id`),
  UNIQUE KEY `uq_category_name` (`cat_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Location
CREATE TABLE `tbl_location` (
  `l_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `c_id` INT UNSIGNED NOT NULL,
  `l_name` VARCHAR(150) NOT NULL,
  `l_details` TEXT NULL,
  PRIMARY KEY (`l_id`),
  KEY `idx_location_country` (`c_id`),
  CONSTRAINT `fk_location_country`
    FOREIGN KEY (`c_id`) REFERENCES `tbl_country` (`c_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Routes
CREATE TABLE `tbl_routes` (
  `r_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `r_start` VARCHAR(150) NOT NULL,
  `r_end` VARCHAR(150) NOT NULL,
  PRIMARY KEY (`r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehicles
CREATE TABLE `tbl_vehicle` (
  `v_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `v_name` VARCHAR(150) NOT NULL,
  `v_description` TEXT NULL,
  `cat_id` INT UNSIGNED NULL,
  PRIMARY KEY (`v_id`),
  KEY `idx_vehicle_category` (`cat_id`),
  CONSTRAINT `fk_vehicle_category`
    FOREIGN KEY (`cat_id`) REFERENCES `tbl_category` (`cat_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assignments / Packages
CREATE TABLE `tbl_assign` (
  `as_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `v_id` INT UNSIGNED NOT NULL,
  `r_id` INT UNSIGNED NOT NULL,
  `l_id` INT UNSIGNED NOT NULL,
  `seats` INT NOT NULL,
  `rate` DECIMAL(10,2) NOT NULL,
  `number_of_days` INT NOT NULL DEFAULT 1,
  `pack_img` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`as_id`),
  KEY `idx_assign_vehicle` (`v_id`),
  KEY `idx_assign_route` (`r_id`),
  KEY `idx_assign_location` (`l_id`),
  CONSTRAINT `fk_assign_vehicle`
    FOREIGN KEY (`v_id`) REFERENCES `tbl_vehicle` (`v_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `fk_assign_route`
    FOREIGN KEY (`r_id`) REFERENCES `tbl_routes` (`r_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,
  CONSTRAINT `fk_assign_location`
    FOREIGN KEY (`l_id`) REFERENCES `tbl_location` (`l_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bookings
CREATE TABLE `tbl_booking` (
  `booking_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `as_id` INT UNSIGNED NOT NULL,

  -- Some pages reference these legacy columns (kept for compatibility)
  `v_id` INT UNSIGNED NULL,
  `date` DATE NULL,
  `seats` INT NULL,

  `status` ENUM('pending','confirmed','canceled') NOT NULL DEFAULT 'pending',
  `booking_date` DATE NULL,
  `booking_day` VARCHAR(20) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `idx_booking_user` (`user_id`),
  KEY `idx_booking_assign` (`as_id`),
  KEY `idx_booking_vehicle` (`v_id`),
  CONSTRAINT `fk_booking_user`
    FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_booking_assign`
    FOREIGN KEY (`as_id`) REFERENCES `tbl_assign` (`as_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_booking_vehicle`
    FOREIGN KEY (`v_id`) REFERENCES `tbl_vehicle` (`v_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ratings
CREATE TABLE `tbl_rating` (
  `rate_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `booking_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `rating` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rate_id`),
  UNIQUE KEY `uq_rating_booking_user` (`booking_id`, `user_id`),
  KEY `idx_rating_user` (`user_id`),
  CONSTRAINT `fk_rating_booking`
    FOREIGN KEY (`booking_id`) REFERENCES `tbl_booking` (`booking_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_rating_user`
    FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `chk_rating_range` CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
CREATE TABLE `tbl_notifications` (
  `notification_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `booking_id` INT UNSIGNED NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `idx_notification_user` (`user_id`),
  KEY `idx_notification_status` (`status`),
  CONSTRAINT `fk_notification_user`
    FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `fk_notification_booking`
    FOREIGN KEY (`booking_id`) REFERENCES `tbl_booking` (`booking_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Seed data (minimal, for first run)
-- ------------------------------------------------------------

INSERT INTO `tbl_user`
  (`user_name`, `user_address`, `user_age`, `user_gender`, `user_phone`, `user_email`, `user_status`, `user_password`, `user_role`)
VALUES
  ('admin',     'Admin Address',     30, 'other', '0000000000', 'admin@example.com',     'active', 'admin123',    'admin'),
  ('organizer', 'Organizer Address', 28, 'other', '0000000001', 'organizer@example.com', 'active', 'organizer123','organizer'),
  ('customer',  'Customer Address',  22, 'other', '0000000002', 'customer@example.com',  'active', 'customer123', 'customer');

INSERT INTO `tbl_country` (`c_name`, `c_description`, `c_status`)
VALUES ('India', 'Seed country', 'active');

INSERT INTO `tbl_category` (`cat_name`, `cat_description`)
VALUES ('Car', 'Seed category');

INSERT INTO `tbl_vehicle` (`v_name`, `v_description`, `cat_id`)
VALUES ('Sedan', 'Seed vehicle', 1);

INSERT INTO `tbl_routes` (`r_start`, `r_end`)
VALUES ('Start Point', 'End Point');

INSERT INTO `tbl_location` (`c_id`, `l_name`, `l_details`)
VALUES (1, 'Sample Location', 'Seed location details');

-- Use an existing file name from /uploads so organizer dropdown works out-of-the-box
INSERT INTO `tbl_assign` (`v_id`, `r_id`, `l_id`, `seats`, `rate`, `number_of_days`, `pack_img`)
VALUES (1, 1, 1, 10, 1000.00, 3, 'areekal.jpg');

INSERT INTO `tbl_faq` (`question`, `answer`, `created_at`)
VALUES ('How do I book a package?', 'Login as a customer and use "Book Package".', NOW());

