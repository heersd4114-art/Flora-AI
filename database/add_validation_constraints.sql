-- Migration: Add Validation Constraints
-- Run this in phpMyAdmin to update the database

USE `plant_app_db`;

-- 1. Update phone column to enforce 10-digit limit
ALTER TABLE `users` 
  MODIFY COLUMN `phone` VARCHAR(10) DEFAULT NULL;

ALTER TABLE `delivery_partners`
  MODIFY COLUMN `contact_number` VARCHAR(10) NOT NULL;

-- Note: Password validation (8+ chars, special character) will be enforced in PHP code
-- Database will continue to store hashed passwords as VARCHAR(255)
