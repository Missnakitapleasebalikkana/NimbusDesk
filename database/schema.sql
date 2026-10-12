-- ===================================================
-- NimbusDesk Aviation IT Incident Management Schema
-- ===================================================

CREATE DATABASE IF NOT EXISTS `nimbusdesk` 
DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `nimbusdesk`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('employee', 'technician', 'admin') NOT NULL DEFAULT 'employee',
    `department` VARCHAR(100) NOT NULL,
    `badge_number` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Issue Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-wrench'
) ENGINE=InnoDB;

-- 3. Tickets Table
CREATE TABLE IF NOT EXISTS `tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_code` VARCHAR(20) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `urgency` ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    `location` VARCHAR(150) NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `attachment` VARCHAR(255) NULL,
    `status` ENUM('Open', 'In Progress', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
    `assigned_to` INT NULL,
    `resolution_summary` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `resolved_at` TIMESTAMP NULL,
    CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tickets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tickets_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Ticket Logs Table
CREATE TABLE IF NOT EXISTS `ticket_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `action_type` VARCHAR(50) NOT NULL,
    `notes` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_logs_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================================================
-- SEED DATA
-- ===================================================

-- Seed Users (password 'password123')
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `badge_number`) VALUES
(1, 'Alice Morgan', 'alice@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'employee', 'Gate Services (Terminal 2)', 'AG-4401'),
(2, 'Bob Chen', 'bob@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'employee', 'Flight Crew Operations', 'FC-8819'),
(3, 'Charlie Santos', 'charlie@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'employee', 'Passenger Check-In', 'CI-2204'),
(4, 'Dave Miller', 'dave@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'technician', 'IT Help Desk', 'IT-1011'),
(5, 'Ethan Vance', 'ethan@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'technician', 'IT Field Support', 'IT-1025'),
(6, 'Sarah Jenkins', 'sarah@airline.com', '$2y$10$eA0X.1P3dI5b1fSfgDmsmOLqM5mH7uC9TkgwWwBvTvhLzX0n2b.U.', 'admin', 'IT Operations Supervisor', 'ADM-001')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Seed Categories
INSERT INTO `categories` (`id`, `name`, `description`, `icon`) VALUES
(1, 'Hardware & Devices', 'Boarding scanners, thermal printers, flight terminals, workstations', 'fa-desktop'),
(2, 'Aviation Software & Apps', 'Check-in systems, flight dispatch tools, manifest software, crew portal', 'fa-laptop-code'),
(3, 'Network & Ground Comms', 'Tarmac Wi-Fi, gate switch drops, VHF data link, terminal internet', 'fa-network-wired'),
(4, 'Flight Display Systems (FIDS)', 'Gate monitors, baggage carousel displays, arrival/departure boards', 'fa-tv'),
(5, 'Other Airport Facility IT', 'Baggage scale sensors, RFID taggers, PA announcements', 'fa-screwdriver-wrench')
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Seed Initial Aviation Tickets
INSERT INTO `tickets` (`id`, `ticket_code`, `user_id`, `category_id`, `urgency`, `location`, `subject`, `description`, `status`, `assigned_to`, `created_at`, `resolved_at`) VALUES
(1, 'NB-10024', 1, 1, 'Critical', 'Terminal 2 - Gate 4B', 'Gate 4 boarding scanner hardware failure', 'The primary Honeywell 2D barcode boarding pass scanner is powered off and unresponsive. Flight AC-382 begins boarding in 30 minutes.', 'Open', NULL, DATE_SUB(NOW(), INTERVAL 45 MINUTE), NULL),
(2, 'NB-10023', 2, 3, 'Medium', 'Flight Ops Dispatch Desk 3', 'Cannot access airline flight routing portal', 'Timeout occurs when opening the operational dispatch module. Ground radio confirms flight schedules are pending release.', 'Open', NULL, DATE_SUB(NOW(), INTERVAL 2 HOUR), NULL),
(3, 'NB-10022', 3, 1, 'Low', 'Terminal 1 - Counter 14', 'Bag tag thermal printer jam and feeder error', 'Printer error light is flashing amber. Bag tags are misaligned on roll feed.', 'In Progress', 4, DATE_SUB(NOW(), INTERVAL 5 HOUR), NULL),
(4, 'NB-10021', 1, 4, 'High', 'Terminal 2 - Baggage Claim 3', 'Arrival display showing scrambled flight data', 'The FIDS monitor above carousel 3 is flickering and displaying out-of-sync flight codes for arriving flight DL-410.', 'In Progress', 5, DATE_SUB(NOW(), INTERVAL 6 HOUR), NULL),
(5, 'NB-10019', 3, 2, 'Medium', 'Check-In Island B', 'Passenger check-in client intermittently freezing', 'Passport OCR scanning causes the check-in software to hang for 20-30 seconds during check-in rush.', 'Resolved', 4, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
(6, 'NB-10015', 2, 3, 'High', 'Gate 2 Bridge', 'No tarmac wireless connectivity on bridge docking', 'Ramp tablet devices cannot sync baggage counts before departure.', 'Resolved', 5, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY))
ON DUPLICATE KEY UPDATE `ticket_code`=VALUES(`ticket_code`);

-- Seed Initial Ticket Activity Logs
INSERT INTO `ticket_logs` (`ticket_id`, `user_id`, `action_type`, `notes`, `created_at`) VALUES
(1, 1, 'Created', 'Support ticket reported by Alice Morgan (Gate Services). Urgency marked as Critical.', DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(2, 2, 'Created', 'Issue submitted by Bob Chen (Flight Crew Ops).', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(3, 3, 'Created', 'Printer issue logged by Charlie Santos.', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(3, 4, 'Assigned', 'Ticket assigned to IT Technician Dave Miller.', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 4, 'Status Updated', 'Technician dispatched with replacement thermal print-head.', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(5, 3, 'Created', 'Software performance ticket opened.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(5, 4, 'Resolved', 'Cleared local cache database and re-indexed passenger registry service.', DATE_SUB(NOW(), INTERVAL 1 DAY))
ON DUPLICATE KEY UPDATE `notes`=VALUES(`notes`);
