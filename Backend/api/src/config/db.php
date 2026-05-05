<?php
class Database {
    private static $instance = null;
    private function __construct() {}
    public static function getInstance() {
        if (self::$instance === null) {
            $host = 'localhost';
            $db   = 'eshop';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
            self::$instance = new PDO($dsn, $user, $pass, $options);
        }
        return self::$instance;
    }
}