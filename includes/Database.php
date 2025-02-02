<?php

class Database {
    private static $instance = null;
    private $mysqli = null;
    private $pdo = null;

    private function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        
        // Create MySQLi connection for legacy code
        $this->mysqli = new mysqli(
            $config['host'],
            $config['username'],
            $config['password'],
            $config['dbname']
        );

        if ($this->mysqli->connect_error) {
            throw new Exception("Connection failed: " . $this->mysqli->connect_error);
        }

        // Create PDO connection for new code
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getMysqli() {
        return $this->mysqli;
    }

    public function getPdo() {
        return $this->pdo;
    }

    // Helper method to maintain compatibility with old code
    public static function getConnection() {
        return self::getInstance()->getMysqli();
    }
}