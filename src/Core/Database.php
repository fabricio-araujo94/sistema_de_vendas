<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        
        $dbName = $_ENV['DB_NAME'];
        
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            $appEnv = $_ENV['APP_ENV'] ?? 'production';
            if ($appEnv === 'development') {
                die("Database connection failed: " . $e->getMessage());
            } else {
                die("A database error occurred. Please contact the administrator.");
            }
        }
    }

    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}