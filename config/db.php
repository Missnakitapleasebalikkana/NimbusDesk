<?php
/**
 * Database Connection & Initialization Helper
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;
    
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $host = 'localhost';
            $user = 'root';
            $pass = '';
            $dbname = 'nimbusdesk';
            
            try {
                // First try direct connection to database
                $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // If database doesn't exist yet, auto initialize from setup
                if (str_contains($e->getMessage(), 'Unknown database')) {
                    require_once __DIR__ . '/../database/setup.php';
                    return self::getConnection();
                }
                throw $e;
            }
        }
        
        return self::$instance;
    }
}
