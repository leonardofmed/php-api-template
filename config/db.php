<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Load dotenv package

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..'); // Path to the directory where .env is located
$dotenv->load();

$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];

$dsn = "mysql:host={$dbHost};dbname={$dbName}";
// ATTR_ERRMODE defines how PDO will report errors
// ATTR_DEFAULT_FETCH_MODE sets the default fetch mode for the PDO instance
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $db = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}