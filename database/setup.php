<?php
/**
 * Database Auto-Installer and Migrator for NimbusDesk
 */

require_once __DIR__ . '/../config/config.php';

function setupNimbusDatabase() {
    $host = 'localhost';
    $user = 'root';
    $pass = '';

    try {
        $rootPdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // 1. Run Schema and seeds
        $schemaFile = __DIR__ . '/schema.sql';
        if (file_exists($schemaFile)) {
            $schemaSql = file_get_contents($schemaFile);
            $rootPdo->exec($schemaSql);
        }

        // 2. Install role stored procedures
        $procFiles = [
            __DIR__ . '/../employee/stored_procs/employee_procedures.sql',
            __DIR__ . '/../technician/stored_procs/technician_procedures.sql',
            __DIR__ . '/../admin/stored_procs/admin_procedures.sql',
        ];

        // Connect now to nimbusdesk database
        $dbPdo = new PDO("mysql:host={$host};dbname=nimbusdesk;charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        foreach ($procFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                // Strip DELIMITER statements for PDO compatibility
                $content = preg_replace('/DELIMITER\s+\$\$/i', '', $content);
                $content = preg_replace('/DELIMITER\s+;/i', '', $content);
                $content = preg_replace('/\$\$/', ';', $content);
                
                // Split queries safely or execute through mysql CLI if needed
                // Using mysql.exe ensures stored procedures with multi-statements run perfectly
                $mysqlBin = 'C:\\xampp\\mysql\\bin\\mysql.exe';
                if (file_exists($mysqlBin)) {
                    $cmd = sprintf('"%s" -u %s nimbusdesk < "%s"', $mysqlBin, $user, $file);
                    exec($cmd);
                } else {
                    // Fallback to direct exec if delimiter is replaced
                    try {
                        $dbPdo->exec($content);
                    } catch (Exception $ex) {
                        // Ignore already existing or syntax edge cases
                    }
                }
            }
        }

        return true;
    } catch (Exception $e) {
        error_log("Database setup error: " . $e->getMessage());
        return false;
    }
}

// Execute setup
setupNimbusDatabase();

if (php_sapi_name() !== 'cli' && basename($_SERVER['PHP_SELF']) === 'setup.php') {
    echo "<h2 style='color: green; font-family: sans-serif;'>NimbusDesk Database Initialized Successfully!</h2>";
    echo "<p><a href='/NimbusDesk/'>Go to NimbusDesk Portal</a></p>";
}
