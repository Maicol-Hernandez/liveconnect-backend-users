<?php

namespace App\Controllers;

use App\Response;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Exceptions\HttpException;

class AuthController
{

    public function login(): Response
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = new User(null, null, $email, $password, null, null);

        if (empty($email) || empty($password)) {
            throw new HttpException("You must send an email and password", 400);
            exit;
        }

        $user = User::getUserEmail($user);

        if (empty($user)) {
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

    public function register(): Response
    {
        return view('json', 'register');
    }
}
