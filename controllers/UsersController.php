<?php

use Firebase\JWT\JWT;

class UsersController {
    
    private $userModel;
    private $jwtSecret = 'your_secret_key';

    public function __construct() {
        $this->userModel = new UserModel();
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
        $requestData = $request->getParsedBody();

        if (isset($requestData['name']) && isset($requestData['email']) && isset($requestData['password'])) {
            try {
                $newUser = $this->userModel->createUser($requestData['name'], $requestData['email'], $requestData['password']);
                return $this->jsonResponse($newUser, 201);
            } catch (Exception $e) {
                return $this->jsonResponse(['error' => $e->getMessage()], 500);
            }
        } else {
            return $this->jsonResponse(['error' => 'Invalid input data'], 400);
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