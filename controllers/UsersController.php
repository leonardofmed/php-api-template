<?php

class UsersController {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function getAllUsers() {
        try {
            $users = $this->userModel->getAllUsers();
            return $this->jsonResponse($users);
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    public function createUser() {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        $requestData = $request->getParsedBody();

        if (isset($requestData['name']) && isset($requestData['email'])) {
            try {
                $newUser = $this->userModel->createUser($requestData['name'], $requestData['email']);
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