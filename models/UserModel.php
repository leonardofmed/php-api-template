<?php

class UserModel {

    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
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