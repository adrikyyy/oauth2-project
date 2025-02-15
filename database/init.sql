-- database/init.sql
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Database: oauth2_system
CREATE DATABASE IF NOT EXISTS `oauth2_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `oauth2_system`;

-- Tabel users
CREATE TABLE `users` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    `failed_attempts` INT DEFAULT 0,
    `last_failed_attempt` TIMESTAMP NULL,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel oauth_clients
CREATE TABLE `oauth_clients` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `client_id` VARCHAR(80) UNIQUE NOT NULL,
    `client_secret` VARCHAR(255) NOT NULL,
    `redirect_uri` TEXT NOT NULL,
    `grant_types` VARCHAR(80),
    `scope` VARCHAR(255),
    `user_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel oauth_access_tokens
CREATE TABLE `oauth_access_tokens` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `access_token` VARCHAR(255) UNIQUE NOT NULL,
    `client_id` INT NOT NULL,
    `user_id` INT,
    `expires` TIMESTAMP NOT NULL,
    `scope` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `oauth_clients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_access_token` (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel oauth_refresh_tokens
CREATE TABLE `oauth_refresh_tokens` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `refresh_token` VARCHAR(255) UNIQUE NOT NULL,
    `client_id` INT NOT NULL,
    `user_id` INT,
    `expires` TIMESTAMP NOT NULL,
    `scope` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `oauth_clients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_refresh_token` (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel oauth_auth_codes
CREATE TABLE `oauth_auth_codes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `authorization_code` VARCHAR(255) UNIQUE NOT NULL,
    `client_id` INT NOT NULL,
    `user_id` INT,
    `redirect_uri` TEXT NOT NULL,
    `expires` TIMESTAMP NOT NULL,
    `scope` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`client_id`) REFERENCES `oauth_clients`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_auth_code` (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;