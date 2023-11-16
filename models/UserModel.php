<?php

class UserModel {

    private $db;

    public function __construct() {
        // Load database configuration
        require_once __DIR__ . '/../config/db.php';

        $dsn = "mysql:host={$dbHost};dbname={$dbName}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        try {
            $this->db = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            throw new Exception('Database connection error: ' . $e->getMessage());
        }
    }

    public function getAllUsers() {
        try {
            $query = $this->db->query("SELECT * FROM users");
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    public function createUser($name, $email) {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->execute([$name, $email]);

            $newUserId = $this->db->lastInsertId();

            $query = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $query->execute([$newUserId]);
            return $query->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
}