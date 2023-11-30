<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class UserModel {

    private $db;

    public function __construct() {
        // Load database configuration
        $this->db = DatabaseConnection::getConnection();
    }

    public function getAllUsers() {
        try {
            $query = $this->db->query("SELECT * FROM users");
            return $query->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    public function createUser($uid, $email, $pwd, $role) {
        try {
            // Hash the password
            $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("INSERT INTO users (uid, email, pwd, role, last_modified) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$uid, $email, $hashedPassword, $role]);

            return 'User created successfully';

            // $newUserId = $this->db->lastInsertId();
            // $query = $this->db->prepare("SELECT * FROM users WHERE uid = ?");
            // $query->execute([$newUserId]);
            // return $query->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
    
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    public function deleteUser($uid) {
        try {
            $query = "DELETE FROM users WHERE uid = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$uid]);

            return $stmt->rowCount(); // Returns the number of affected rows
        } catch (PDOException $e) {
            throw new Exception('Error deleting user: ' . $e->getMessage());
        }
    }
}