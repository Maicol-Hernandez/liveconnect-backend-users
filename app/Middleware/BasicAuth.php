<?php

namespace App\Middleware;

use Exception;

use App\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\key;
use App\Middleware\Middleware;
use App\Exceptions\HttpException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class BasicAuth extends Middleware
{
    public function handle(Request $request): Request
    {
        if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
            throw new HttpException('You must send Authorization header', 422);
        }

        $token = $_SERVER['HTTP_AUTHORIZATION'];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], 'HS256'));
            $request->input('user_id', $decoded->data->id);
            return $request;
        } catch (ExpiredException $e) {
            // error 401 Unauthorized
            throw new HttpException('Your token has expired, please login again', 401);
        } catch (SignatureInvalidException $e) {
            // error 401 Unauthorized
            throw new HttpException("Your token has expired, please login again", 401);
        } catch (Exception $e) {
            //TODO: If something happens that we do not take into account, we will notify you here 
            throw new HttpException('An error has occurred, please log in again, if it persists, please contact the admin');
        }
    }
}
