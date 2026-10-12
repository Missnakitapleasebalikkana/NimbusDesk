<?php
/**
 * NimbusDesk Database Setup & Seeder Script
 * Run this script to automatically create the database, tables, and seed initial accounts.
 * Group 4: Dig, Magbagay, Samaniego, Villarin
 */

date_default_timezone_set('Asia/Manila');

$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = ''; // Default XAMPP MySQL password is empty

echo "=== NimbusDesk Database Setup ===\n";

try {
    // 1. Connect to MySQL server without selecting database
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $pdo->exec("SET time_zone = '+08:00'");
    echo "[✓] Connected to MySQL server successfully.\n";

    // 2. Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `nimbusdesk_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `nimbusdesk_db`;");
    $pdo->exec("SET time_zone = '+08:00'");
    echo "[✓] Selected database `nimbusdesk_db`.\n";

    // 3. Create tables with updated aviation columns and soft-delete safeguards
    $pdo->exec("
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
    ");
    echo "[✓] Fresh tables (`users`, `tickets`, `ticket_notes`) created successfully with aviation fields.\n";

    // 4. Seed Users according to PDF target users
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    $techPass = password_hash('tech123', PASSWORD_DEFAULT);
    $empPass = password_hash('employee123', PASSWORD_DEFAULT);

    $userStmt = $pdo->prepare("
        INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `phone`, `status`, `created_at`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())
    ");

    // IT Supervisors / System Administrators (PDF Target User 3)
    $userStmt->execute([1, 'System Administrator', 'admin@nimbusdesk.com', $adminPass, 'admin', 'IT Operations & Supervision', '+63 (02) 8876-1001']);
    
    // IT Help Desk Technicians (PDF Target User 2)
    $userStmt->execute([2, 'Alex Rivera', 'tech1@nimbusdesk.com', $techPass, 'technician', 'Aviation Hardware & Support', '+63 (02) 8876-1002']);
    $userStmt->execute([3, 'Sarah Chen', 'tech2@nimbusdesk.com', $techPass, 'technician', 'Airport Network Operations', '+63 (02) 8876-1003']);
    
    // Airline / Airport Employees (PDF Target User 1)
    $userStmt->execute([4, 'Capt. Mark Davis', 'agent@airline.com', $empPass, 'employee', 'Flight Crew / Operations', '+63 (02) 8876-2001']);
    $userStmt->execute([5, 'Elena Rostova', 'gate@airline.com', $empPass, 'employee', 'Gate & Ground Services', '+63 (02) 8876-2002']);

    echo "[✓] Seeded target users adhering to NimbusDesk specification.\n";

    // 5. Seed Realistic Aviation Incidents with Terminal and Location info
    $pdo->exec("
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
    ");

    echo "[✓] Seeded clean, realistic aviation incident tickets and audit notes.\n";
    echo "\n=== Database Clean Reset Finished Successfully! ===\n";

} catch (PDOException $e) {
    echo "\n[ERROR] Database setup failed:\n" . $e->getMessage() . "\n";
    echo "Tip: Ensure MySQL is running in your XAMPP Control Panel.\n";
    exit(1);
}
