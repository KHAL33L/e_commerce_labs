<?php
// classes/db_connection.php
// Simple PDO wrapper - change credentials to your local setup.

class DBConnection {
    protected $pdo;

    public function __construct() {
        $host = 'localhost';
        $db   = 'ecommerce_2025A_ibrahim_dasuki';
        $user = 'ibrahim.dasuki';
        $pass = 'Delorean12!'; 
        $charset = 'utf8mb4';

        // $host = '127.0.0.1';
        // $db   = 'dbforlab';
        // $user = 'root';
        // $pass = ''; 
        // $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $opts);
        } catch (PDOException $e) {
            // In production, log this instead of echoing
            exit('DB Connection failed: ' . $e->getMessage());
        }
    }

    public function getPDO() {
        return $this->pdo;
    }
}
