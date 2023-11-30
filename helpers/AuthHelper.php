<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthHelper {
    private $jwtSecret;

    public function __construct($jwtSecret) {
        $this->jwtSecret = $jwtSecret;
    }

    public function getTokenFromHeaders() {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        return str_replace('Bearer ', '', $authorizationHeader);
    }

    public function verifyToken($token) {
        try {
            JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return true; // Token is valid
        } catch (Exception $e) {
            return false; // Token verification failed
        }
    }

    public function authenticate() {
        $token = $this->getTokenFromHeaders();
        if (!$token) {
            return false; // No token provided
        }
        return $this->verifyToken($token);
    }
}