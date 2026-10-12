<?php

namespace NimbusDesk\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Singleton class to provide and maintain a single PDO database connection.
 */
class Database {
    private static ?PDO $instance = null;

    private static string $host = '127.0.0.1';
    private static string $dbName = 'nimbusdesk_db';
    private static string $user = 'root';
    private static string $pass = '';
    private static int $port = 3306;

    /**
     * Private constructor to prevent direct creation.
     */
    private function __construct() {}

    /**
     * Get the PDO database connection instance.
     *
     * @return PDO
     */
    public static function getConnection(): PDO {
        if (!date_default_timezone_get() || date_default_timezone_get() === 'UTC' || date_default_timezone_get() === 'Europe/Berlin') {
            date_default_timezone_set('Asia/Manila');
        }

        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$dbName . ";charset=utf8mb4";
                self::$instance = new PDO($dsn, self::$user, self::$pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
                self::$instance->exec("SET time_zone = '+08:00'");
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
