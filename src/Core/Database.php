<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;

    private PDO $connection;

    private function __construct() {
        $host = "localhost";
        $dbName = "sales_system";
        $username = "root";
        $password = "";
        $charset = "utf8mb4";
        
        $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
    
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
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