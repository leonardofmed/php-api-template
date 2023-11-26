<?php


require_once __DIR__ . '/vendor/autoload.php'; // Autoload classes using Composer
require_once __DIR__ . '/config/db.php'; // Database configuration
require_once __DIR__ . '/controllers/UsersController.php';

use FastRoute\RouteCollector;
use Psr\Http\Message\ResponseInterface;
use FastRoute\Dispatcher;
use GuzzleHttp\Psr7\ServerRequest;

// Create a route dispatcher
$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('GET', '/api/users', ['UsersController', 'getAllUsers']);
    $r->addRoute('POST', '/api/users', ['UsersController', 'createUser']);
    $r->addRoute('GET', '/api/users/{email}', ['UsersController', 'getUserByEmail']);
    $r->addRoute('POST', '/api/login', ['UsersController', 'login']);
});

// Fetch method and URI from the server and match to a route
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(["error" => "Route not found"]);
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $controllerName = $handler[0];
        $method = $handler[1];

        $controller = new $controllerName();

        $request = ServerRequest::fromGlobals();

        try {
            // Basic input validation example (you might use a validation library)
            $requestData = $request->getParsedBody();
            // Perform input validation on $requestData
            
            // Implement security measures (authentication, authorization, etc.) here
            
            // Call the controller method
            $response = call_user_func_array([$controller, $method], $vars);
            
            if ($response instanceof ResponseInterface) {
                // Send the response
                echo (string) $response->getBody();
            } else {
                // Handle other types of responses accordingly
                echo json_encode($response);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
        break;
}