-- NimbusDesk Database Schema & Initial Clean Aviation Data
-- Group 4: Dig, Magbagay, Samaniego, Villarin

CREATE DATABASE IF NOT EXISTS `nimbusdesk_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nimbusdesk_db`;
SET time_zone = '+08:00';

-- 1. Users Table (Role-Based Access Control)
DROP TABLE IF EXISTS `ticket_notes`;
DROP TABLE IF EXISTS `tickets`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('employee', 'technician', 'admin') NOT NULL DEFAULT 'employee',
    `department` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(30) DEFAULT NULL,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `reset_requested_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tickets Table
CREATE TABLE `tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_code` VARCHAR(20) NOT NULL UNIQUE,
    `user_id` INT NOT NULL,
    `assigned_to` INT DEFAULT NULL,
    `terminal` VARCHAR(50) NOT NULL DEFAULT 'Terminal 1',
    `location` VARCHAR(100) DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `category` ENUM('Hardware', 'Software', 'Network', 'Facilities', 'Other') NOT NULL DEFAULT 'Software',
    `urgency` ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    `status` ENUM('Open', 'In Progress', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
    `description` TEXT NOT NULL,
    `attachment` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `resolved_at` DATETIME DEFAULT NULL,
    CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tickets_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Ticket Notes / Incident Audit Timeline Table
CREATE TABLE `ticket_notes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `note` TEXT NOT NULL,
    `status_change` VARCHAR(50) DEFAULT NULL,
    `is_internal` TINYINT(1) DEFAULT 0,
    `attachment` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notes_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Accounts (Passwords: admin123, tech123, employee123)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `phone`, `status`, `created_at`) VALUES
(1, 'System Administrator', 'admin@nimbusdesk.com', '$2y$10$ZiWtuWQbFpo7sx.hCu9PwuimAcuPTTOUyn9/ehtzi.2VvZQsfifaO', 'admin', 'IT Operations & Supervision', '+63 (02) 8876-1001', 'active', NOW()),
(2, 'Alex Rivera', 'tech1@nimbusdesk.com', '$2y$10$X143TB/1mRekKwuiR377Pe3tYJpfPGQOUr1arQhjbp3jEcxkB7OZ2', 'technician', 'Aviation Hardware & Support', '+63 (02) 8876-1002', 'active', NOW()),
(3, 'Sarah Chen', 'tech2@nimbusdesk.com', '$2y$10$X143TB/1mRekKwuiR377Pe3tYJpfPGQOUr1arQhjbp3jEcxkB7OZ2', 'technician', 'Airport Network Operations', '+63 (02) 8876-1003', 'active', NOW()),
(4, 'Capt. Mark Davis', 'agent@airline.com', '$2y$10$TQM8nOENftHM8lNbO72dC.aVhZX3PT3HTOtD4PxfCemJRo/6Lzf.m', 'employee', 'Flight Crew / Operations', '+63 (02) 8876-2001', 'active', NOW()),
(5, 'Elena Rostova', 'gate@airline.com', '$2y$10$TQM8nOENftHM8lNbO72dC.aVhZX3PT3HTOtD4PxfCemJRo/6Lzf.m', 'employee', 'Gate & Ground Services', '+63 (02) 8876-2002', 'active', NOW());

-- Seed Initial Realistic Aviation Incidents
INSERT INTO `tickets` (`id`, `ticket_code`, `user_id`, `assigned_to`, `terminal`, `location`, `subject`, `category`, `urgency`, `status`, `description`, `attachment`, `created_at`, `updated_at`, `resolved_at`) VALUES
(1, 'TKT-1001', 5, NULL, 'Terminal 1', 'Gate 4 - Departure Bridge', 'Gate 4 boarding pass scanner hardware failure', 'Hardware', 'Critical', 'Open', 'The primary optical barcode scanner at Gate 4 failed during pre-boarding for flight ND-402. Power indicator flashes red and device fails to scan passenger mobile boarding passes.', NULL, NOW() - INTERVAL 45 MINUTE, NOW() - INTERVAL 45 MINUTE, NULL),
(2, 'TKT-1002', 4, 2, 'Terminal 2', 'Dispatch Ops Desk 3', 'Flight dispatch weather radar telemetry sync glitch', 'Software', 'High', 'In Progress', 'The electronic flight bag (EFB) weather sync is intermittently disconnecting from the dispatch telemetry feed during pre-flight departure checks.', NULL, NOW() - INTERVAL 3 HOUR, NOW() - INTERVAL 1 HOUR, NULL),
(3, 'TKT-1003', 5, 3, 'Terminal 2', 'Crew Briefing Lounge B', 'Terminal 2 Crew Lounge Wi-Fi connectivity lost', 'Network', 'Medium', 'In Progress', 'Access point AP-T2-Lounge is offline. Cabin crew and flight attendants unable to download roster updates before departure.', NULL, NOW() - INTERVAL 5 HOUR, NOW() - INTERVAL 2 HOUR, NULL),
(4, 'TKT-1004', 4, 2, 'Terminal 3', 'Arrival Hall Carousel 3', 'Baggage sorting conveyor display terminal freeze', 'Hardware', 'High', 'Resolved', 'Carrousel 3 monitoring touch screen was completely frozen displaying error code ERR_GPU_HANG. System rebooted and display adapter replaced.', NULL, NOW() - INTERVAL 24 HOUR, NOW() - INTERVAL 18 HOUR, NOW() - INTERVAL 18 HOUR),
(5, 'TKT-1005', 5, 3, 'Terminal 1', 'Check-in Counters 12-16', 'Departure Control System printer spooler jam', 'Software', 'High', 'Resolved', 'Boarding pass printer spooler server service crashed causing luggage tag queue delays. Restarted CUPS spooler service and purged stalled queues.', NULL, NOW() - INTERVAL 36 HOUR, NOW() - INTERVAL 32 HOUR, NOW() - INTERVAL 32 HOUR),
(6, 'TKT-1006', 4, NULL, 'Domestic Cargo', 'Bay 7 Cargo Manifest Office', 'Cargo weighing scale digital indicator communication error', 'Facilities', 'Low', 'Open', 'Serial communication between RS-232 freight weight indicator and the air cargo manifest workstation dropped.', NULL, NOW() - INTERVAL 2 HOUR, NOW() - INTERVAL 2 HOUR, NULL);

INSERT INTO `ticket_notes` (`ticket_id`, `user_id`, `note`, `status_change`, `is_internal`, `created_at`) VALUES
(2, 2, 'Assigned to Alex Rivera. Telemetry server handshake verified; currently running diagnostic packet test with EFB simulator.', 'Status changed to In Progress', 0, NOW() - INTERVAL 1 HOUR),
(3, 3, 'Sarah Chen assigned. Power over Ethernet (PoE) switch in comm closet 2B rebooted; waiting for crew confirmation.', 'Status changed to In Progress', 0, NOW() - INTERVAL 2 HOUR),
(4, 2, 'Replaced faulty HDMI transmitter module. Terminal rebooted and test sequence passed with 100% telemetry sync.', 'Status changed to Resolved', 0, NOW() - INTERVAL 18 HOUR),
(5, 3, 'Restarted airport print spooler daemon and cleared stalled queue. All check-in desks printing normally.', 'Status changed to Resolved', 0, NOW() - INTERVAL 32 HOUR);
