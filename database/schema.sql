-- Employee Feedback System - Database Schema
-- Run this file to set up the database

CREATE DATABASE IF NOT EXISTS employee_feedback_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE employee_feedback_db;

-- Users table (both Admin and Employee)
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,          -- bcrypt hash
    role        ENUM('admin','employee') NOT NULL DEFAULT 'employee',
    department  VARCHAR(100)        DEFAULT NULL,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT                 NOT NULL,           -- employee who submitted
    category    ENUM('work_environment','management','career_growth','team_collaboration','work_life_balance','other')
                                    NOT NULL DEFAULT 'other',
    rating      TINYINT UNSIGNED    NOT NULL CHECK (rating BETWEEN 1 AND 5),
    message     TEXT                NOT NULL,
    is_anonymous TINYINT(1)         NOT NULL DEFAULT 0,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed: default admin account  (password: Admin@123)
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@company.com',
 '$2y$12$6JCOzSQoVKCVSJBdQ9VdnOH7HBhG/Kn0E2wPzaCAiuZl2n5a0xJLK',
 'admin')
ON DUPLICATE KEY UPDATE id = id;
