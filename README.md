# PHP API with FastRoute

This project aims to create a template API using PHP along with FastRoute for efficient routing. It focuses on implementing RESTful endpoints for user management operations like creating users, retrieving user information, and implementing JWT-based authentication.

## Overview

The API built with FastRoute is designed to handle user-related operations through RESTful endpoints. Key features and functionalities include:

1. User Management:
    - Creating new users via POST requests.
    - Retrieving user details by email or ID through GET requests.

2. Authentication with JWT:
    - Implementing JSON Web Token (JWT)-based authentication for secure user access.
    - Generating and validating JWT tokens for user authentication.

3. Database Interaction:
    - Interaction with a MySQL database using PDO for user data storage and retrieval.
    - Secure password storage by hashing user passwords before storing them in the database.

4. FastRoute Integration:
    - Efficient routing using FastRoute for handling API endpoints.
    - Handling various HTTP methods (GET, POST) for different functionalities.

5. Error Handling and Validation:
    - Robust error handling for different scenarios, including missing request bodies and invalid endpoints.
    - Basic input validation for incoming data to ensure completeness and integrity.

## Setup

### Prerequisites
- PHP
- Composer
- Any database system

### Installation

1. Clone the repository.
2. Install dependencies using Composer:
   `composer install`

3. Configure the environment variables:
   - Create a .env file based on .env.example and add necessary configuration.


### Usage

1. Start the server:
2. Access the endpoints using your preferred API client (e.g., Postman).
    - For example, to create a new user:
        - Method: POST
        - URL: http://localhost:1337/api/users
        - Body: JSON payload containing name, email, and password.