<?php
require_once __DIR__ . './db.php';

class DatabaseConnection {
    private static $db;

    public static function getConnection() {
        if (!isset(self::$db)) {
            self::$db = self::initializeConnection();
        }
        return self::$db;
    }

    private static function initializeConnection() {
        global $db; // Access the $db variable from db.php
        return $db;
    }
}