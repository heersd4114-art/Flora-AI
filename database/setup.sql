-- Database Setup for Plant App (Flora AI)
-- Run this script in phpMyAdmin
-- This schema matches the ACTUAL running database exactly (12 tables)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `plant_app_db`
--
CREATE DATABASE IF NOT EXISTS `plant_app_db`;
USE `plant_app_db`;

-- Drop tables in correct FK order
DROP TABLE IF EXISTS `shipments`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `disease_treatments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `delivery_partners`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `diseases`;
DROP TABLE IF EXISTS `plant_history`;
DROP TABLE IF EXISTS `plants`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer','delivery_partner') DEFAULT 'customer',
  `created_at` timestamp DEFAULT current_timestamp(),
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`name`, `email`, `password`, `role`, `phone`) VALUES
('Admin User', 'admin@flora.com', 'admin123', 'admin', '1234567890'),
('John Doe', 'john@example.com', 'password123', 'customer', '0987654321'),
('Fast Delivery Co.', 'rider@delivery.com', 'delivery123', 'delivery_partner', '1122334455');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('Pesticide','Fertilizer','Tool','Other') NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `products` (`name`, `type`, `description`, `price`, `stock_quantity`, `image_url`) VALUES
('Organic Neem Oil', 'Pesticide', 'Natural pesticide for aphids and mites.', 15.00, 50, 'uploads/plant_69623e63e9d68.jfif'),
('NPK Fertilizer', 'Fertilizer', 'Balanced nutrient mix for growth.', 25.50, 30, 'uploads/plant_69623f9413cf7.jfif'),
('Garden Trowel', 'Tool', 'Heavy duty stainless steel trowel.', 10.00, 40, 'uploads/plant_6962451e13022.jfif'),
('Fungicide Spray', 'Pesticide', 'Treats fungal infections and rust.', 12.99, 20, 'uploads/plant_696261447fba5.jfif');

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `plant_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `scientific_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `care_level` enum('Easy','Medium','Hard') DEFAULT 'Medium',
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`plant_id`),
  KEY `category_id` (`category_id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `disease_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `symptoms` text DEFAULT NULL,
  `care_tips` text DEFAULT NULL,
  PRIMARY KEY (`disease_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `diseases` (`name`, `symptoms`, `care_tips`) VALUES
('Leaf Blight', 'Brown spots on leaves, yellowing.', 'Remove infected leaves, improve air circulation.'),
('Aphid Infestation', 'Small green bugs on stems.', 'Spray with water or Neem Oil.'),
('Root Rot', 'Mushy roots, wilting.', 'Reduce watering, repot separately.');

-- --------------------------------------------------------

--
-- Table structure for table `disease_treatments`
-- Links Diseases to recommended Products
--

CREATE TABLE `disease_treatments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `disease_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`disease_id`) REFERENCES `diseases`(`disease_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `disease_treatments` (`disease_id`, `product_id`) VALUES
(2, 1), -- Aphids -> Neem Oil
(1, 4); -- Blight -> Fungicide

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `created_at` timestamp DEFAULT current_timestamp(),
  `street` varchar(100) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `plant_history`
--

CREATE TABLE `plant_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `plant_name` varchar(100) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `disease_detected` varchar(100) DEFAULT NULL,
  `care_tips` text DEFAULT NULL,
  `scan_date` timestamp DEFAULT current_timestamp(),
  `ai_analysis` text DEFAULT NULL,
  `treatment_steps` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_partners`
--

CREATE TABLE IF NOT EXISTS `delivery_partners` (
  `partner_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `status` enum('Available','Busy','Offline') DEFAULT 'Available',
  PRIMARY KEY (`partner_id`),
  KEY `user_id` (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `delivery_partners` (`name`, `contact_number`, `vehicle_type`, `status`) VALUES
('Speedy Logistics', '555-0101', 'Van', 'Available'),
('Rapid Riders', '555-0102', 'Bike', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE IF NOT EXISTS `shipments` (
  `shipment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `assigned_at` timestamp DEFAULT current_timestamp(),
  `delivered_at` timestamp NULL DEFAULT NULL,
  `tracking_number` varchar(50) DEFAULT NULL,
  `status` enum('Pending','Picked Up','In Transit','Out for Delivery','Delivered','Returned') DEFAULT 'Pending',
  PRIMARY KEY (`shipment_id`),
  KEY `order_id` (`order_id`),
  KEY `partner_id` (`partner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Constraints for dumped tables
--

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

ALTER TABLE `shipments`
  ADD CONSTRAINT `shipments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipments_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `delivery_partners` (`partner_id`) ON DELETE SET NULL;

COMMIT;
