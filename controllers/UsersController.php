<?php

class UsersController {
    
    public function getAllUsers() {
        // Fetch all users 
        // (dummy data for demonstration)
        $users = [
            ['id' => 1, 'name' => 'John Doe'],
            ['id' => 2, 'name' => 'Alice Smith'],
            // ... more user data
        ];

        // Return a JSON response with all users
        return $this->jsonResponse($users);
    }

    public function createUser() {
        $request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        $requestData = $request->getParsedBody();

        // Perform input validation (simplified for demonstration)
        if (isset($requestData['name']) && isset($requestData['email'])) {
            // Validating and creating a new user (dummy response for demonstration)
            $newUser = [
                'id' => 3, // Simulating a new user ID
                'name' => $requestData['name'],
                'email' => $requestData['email']
            ];

            // Return a JSON response with the newly created user
            return $this->jsonResponse($newUser, 201); // 201: Created
        } else {
            return $this->jsonResponse(['error' => 'Invalid input data'], 400); // 400: Bad Request
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