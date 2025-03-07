<?php

namespace App\Middleware;

use Exception;
use App\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Middleware\Middleware;
use App\Exceptions\HttpException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class AdminAuth extends Middleware
{

    public function handle(Request $request): Request
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            // error 422 Unprocessable Entity
            throw new HttpException("You must send Authorization header", 401);
        }

        $token = $_SERVER['HTTP_AUTHORIZATION'];

        try {
            // we generate a token
            $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], 'HS256'));
            $request->input('user_id', $decoded->data->id);
        } catch (ExpiredException $e) {
            // error 401 Unauthorized
            throw new HttpException("Your token has expired, please login again", 401);
        } catch (SignatureInvalidException $e) {
            // error 401 Unauthorized
            throw new HttpException("Your token has expired, please login again", 401);
        } catch (Exception $e) {
            //TODO: If something happens that we do not take into account, we will notify you here 
            throw new HttpException("An error has occurred when you make login, if it persists, please contact");
        }

        $user = User::findById($decoded->data->id);

        if (empty($user)) {
            // error 404 Not Found
            throw new HttpException("User not exists", 404);
        }

        // if you are an admin equal to 1 = true
        // if you are an admin equal to 0 = false 
        // if ($user->is_admin != true) {
        //     // Not admin
        //     throw new HttpException("You don't have the require permissions", 403); // error 403 Forbidden 
        // }

        return $request;
    }
}
