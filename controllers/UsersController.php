<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once __DIR__ . '/../models/UserModel.php';

class UsersController {
    
    private $userModel;
    private $jwtSecret = 'your_secret_key';

    public function __construct() {
        $this->userModel = new UserModel();
    }

    private function getTokenFromHeaders() {
        $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        return str_replace('Bearer ', '', $authorizationHeader);
    }

    private function verifyToken($token) {
        try {
            // Verify the token using the JWT library and the secret key
            JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return true; // Token is valid
        } catch (Exception $e) {
            return false; // Token verification failed
        }
    }

    private function authenticate() {
        $token = $this->getTokenFromHeaders();
        if (!$token) {
            return false; // No token provided
        }
        return $this->verifyToken($token);
    }

    public function login() {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        $requestData = $request->getParsedBody();

        if (isset($requestData['email']) && isset($requestData['password'])) {
            try {
                $user = $this->userModel->getUserByEmail($requestData['email']);
                
                if ($user && password_verify($requestData['password'], $user['password'])) {
                    // Generate JWT token
                    $tokenPayload = ['user_id' => $user['id'], 'email' => $user['email']];
                    $jwtToken = JWT::encode($tokenPayload, $this->jwtSecret, 'HS256');

                    // Return token as JSON response
                    return $this->jsonResponse(['token' => $jwtToken]);
                } else {
                    return $this->jsonResponse(['error' => 'Invalid credentials'], 401);
                }
            } catch (Exception $e) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            return $this->jsonResponse(['error' => 'Invalid input data'], 400);
        }
    }

    public function getAllUsers() {
        try {
            $users = $this->userModel->getAllUsers();
            return $this->jsonResponse($users);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getUserByEmail($email) {
        $decodedToken = $this->authenticate();
        if (!$decodedToken) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $user = $this->userModel->getUserByEmail($email);
            if ($user) {
                return $this->jsonResponse($user);
            } else {
                return $this->jsonResponse(['error' => 'User not found'], 404);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function createUser() {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        $contentType = $request->getHeaderLine('Content-Type');
        $requestData = [];

        if ($contentType === 'application/json') {
            $body = $request->getBody()->getContents();
            $requestData = json_decode($body, true);
        } else {
            $requestData = $request->getParsedBody();
        }

        // Check if request body exists and is not empty
        if (!$requestData || empty($requestData)) {
            return $this->jsonResponse(['error' => 'No data provided in the request body', 'request' => $requestData], 400);
        }

        if (isset($requestData['uid']) && isset($requestData['email']) && isset($requestData['password']) && isset($requestData['role'])) {
            try {
                $newUser = $this->userModel->createUser($requestData['uid'], $requestData['email'], $requestData['password'], $requestData['role']);
                return $this->jsonResponse($newUser, 201);
            } catch (Exception $e) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            return $this->jsonResponse(['error' => 'Incomplete data in the request body'], 400);
        }
    }

    // Helper function for consistent JSON responses
    private function jsonResponse($data, $statusCode = 200) {
        $response = new \GuzzleHttp\Psr7\Response();
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withStatus($statusCode);
        $response->getBody()->write(json_encode($data));
        return $response;
    }
}