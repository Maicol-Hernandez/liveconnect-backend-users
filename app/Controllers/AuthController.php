<?php

namespace App\Controllers;

use Api\Maicoldev\Exceptions\HttpException;
use App\Models\User;
use Firebase\JWT\JWT;

/**
 * 
 */
class AuthController
{
    /**
     * 
     */
    public function auth()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = new User(null, null, $email, $password, null, null);

        if (empty($email) || empty($password)) {
            // HttpException error 400 Bad request
            throw new HttpException("You must send an email and password", 400);
            exit;
        }

        $user = User::getUserEmail($user);

        if (empty($user)) {
            // error 404  Not Found
            throw new HttpException("User not found", 404);
        }

        // if active is equal to 0 = false 
        // if active is equal to 1 = true
        if ($user['is_active'] === false) {
            // Not active
            throw new HttpException("Inactive user", 400); // error 400 Bad Requets
        }

        if (!password_verify($password, $user['password'])) {
            // error 400 Bad Requets
            throw new HttpException("Invalid user or password", 400);
        }

        $time = time();

        $payload = [
            'data' => [
                'id' => $user['id'],
            ],
            'iat' => $time,
            'exp' => $time + (60 * 40)
        ];

        // echo json_encode($payload);
        $jwt = JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');

        return view('json', ['token' => $jwt]);
    }
}
