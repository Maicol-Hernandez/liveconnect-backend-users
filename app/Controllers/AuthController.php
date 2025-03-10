<?php

namespace App\Controllers;

use Throwable;
use App\Request;
use App\Response;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Models\PetUser;
use App\Database\Connection;
use App\Validation\Validator;
use App\Exceptions\HttpException;
use Exception;

class AuthController
{

    private function generateJwtToken(array $user, ?int $issuedAt = null, int $expiresIn = 2400): string
    {
        $currentTime = $issuedAt ?? time();
        $payload = [
            'data' => $user,
            'iat' => $currentTime,
            'exp' => $currentTime + $expiresIn
        ];

        return JWT::encode($payload, $_ENV['JWT_KEY'], 'HS256');
    }

    public function login(Request $request): Response
    {
        $validator = new Validator($request->all(), [
            'email' => ['required', 'email', 'exists'],
            'password' => ['required', 'password'],
        ]);

        $validator->validate();

        $user = User::findByEmail($request->get('email'));

        if (!User::verifyPassword($user, $request->get('password'))) {
            throw new HttpException("Invalid user or password", 400);
        }

        $token = $this->generateJwtToken($user, time());

        return view('json', [...$user, 'token' => $token], 200);
    }

    public function register(Request $request): Response
    {
        $validator = new Validator($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique'],
            'password' => ['required', 'password', 'confirmed'],
            "pets" => ['required', 'array'],
        ]);

        $validator->validate();

        Connection::getInstance()->beginTransaction();
        try {
            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];

            $user = User::create($userData);
            PetUser::createBulk($user['id'], $request->input('pets'));

            Connection::getInstance()->commit();

            $token = $this->generateJwtToken($user, time());

            return view('json', [...$user, 'token' => $token], 201);
        } catch (Throwable $th) {
            Connection::getInstance()->rollback();
            throw new HttpException($th->getMessage(), 500, $th);
        }
    }

    public function logout(Request $request): Response
    {
        // TODO: implement logic for blacklisting tokens

        return view('json', 'Logged out successfully', 200);
    }
}
