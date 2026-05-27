-- This script runs on first-time MySQL volume initialization.
-- It ensures the application user exists and has full privileges
-- on the application database (MySQL 8.x compatible).

CREATE DATABASE IF NOT EXISTS `MockDasherDB`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'mockdasher'@'%' IDENTIFIED BY 'MockDasher_DB_2024!';

ALTER USER 'mockdasher'@'%' IDENTIFIED BY 'MockDasher_DB_2024!';

GRANT ALL PRIVILEGES ON `MockDasherDB`.* TO 'mockdasher'@'%';

FLUSH PRIVILEGES;
