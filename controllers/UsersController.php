<?php

use Firebase\JWT\JWT;
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';

class UsersController {
    
    private $userModel;
    private $jwtSecret;
    private $authHelper;

    public function __construct() {
        $this->userModel = new UserModel();

        $jwtConfig = require_once __DIR__ . '/../config/jwt.php';
        $this->jwtSecret = $jwtConfig['jwtSecret'] ?? null;

        $this->authHelper = new AuthHelper($this->jwtSecret);
    }

    public function login() {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();  
        $contentType = $request->getHeaderLine('Content-Type');    
        $requestData = [];

        if ($contentType === 'application/json') {
            $body = $request->getBody()->getContents();
            $requestData = json_decode($body, true);
        } else {
            $requestData = $request->getParsedBody();
        }

        if (isset($requestData['email']) && isset($requestData['pwd'])) {
            try {
                $user = $this->userModel->getUserByEmail($requestData['email']);
                
                if ($user && password_verify($requestData['pwd'], $user['pwd'])) {
                    // Generate JWT token
                    $tokenPayload = ['uid' => $user['uid'], 'email' => $user['email']];
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
        $decodedToken = $this->authHelper->authenticate();
        if (!$decodedToken) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }
        
        try {
            $users = $this->userModel->getAllUsers();
            return $this->jsonResponse($users);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function getUserByEmail() {
        $decodedToken = $this->authHelper->authenticate();
        if (!$decodedToken) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        $queryParams = $_GET;
        $email = $queryParams['user'] ?? null; // 'user' is the key for the email parameter

        if ($email === null) {
            return $this->jsonResponse(['error' => 'Email parameter is missing'], 400);
        }

        try {
            $user = $this->userModel->getUserByEmail($email);
            if ($user) {
                unset($user['pwd']); // Remove pwd from response
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

        if (isset($requestData['uid']) && isset($requestData['email']) && isset($requestData['pwd']) && isset($requestData['role'])) {
            try {
                $newUser = $this->userModel->createUser($requestData['uid'], $requestData['email'], $requestData['pwd'], $requestData['role']);
                return $this->jsonResponse($newUser, 201);
            } catch (Exception $e) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            return $this->jsonResponse(['error' => 'Incomplete data in the request body'], 400);
        }
    }

    public function deleteUser($uid) {
        if (!$uid) {
            return $this->jsonResponse(['error' => 'User ID not provided'], 400);
        }
        
        $decodedToken = $this->authHelper->authenticate();
        if (!$decodedToken) {
            return $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $rowsAffected = $this->userModel->deleteUser($uid);
            if ($rowsAffected > 0) {
                return $this->jsonResponse(['success' => 'User deleted successfully']);
            } else {
                return $this->jsonResponse(['error' => 'User not found'], 404);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
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