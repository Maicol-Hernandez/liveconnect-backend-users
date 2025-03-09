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
        try {
            $validator = new Validator($request->all(), [
                'email' => ['required', 'email', 'exists'],
                'password' => ['required', 'password'],
            ]);

            $validator->validate();

            $user = User::findByEmail($request->get('email'));

            if (User::verifyPassword($user, $request->get('password'))) {
                throw new HttpException("Invalid user or password", 400);
            }

            $token = $this->generateJwtToken($user, time());

            return view('json', [...$user, 'token' => $token], 200);
        } catch (Throwable $th) {
            throw new HttpException($th->getMessage(), 500, $th);
        }
    }

    public function register(Request $request): Response
    {
        Connection::getInstance()->beginTransaction();
        try {
            $validator = new Validator($request->all(), [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique'],
                'password' => ['required', 'password'],
                "pets" => ['required', 'array'],
            ]);

            $validator->validate();

            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];

            $user = User::create($userData);
            PetUser::createBulk($user['id'], $request->input('pets'));

            Connection::getInstance()->commit();

            return view('json', $user, 201);
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
