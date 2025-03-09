<?php

namespace App\Middleware;

use Exception;
use Throwable;
use App\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Middleware\Middleware;
use App\Exceptions\HttpException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class BasicAuth extends Middleware
{
    /**
     * Handle the JWT authentication
     *
     * @param Request $request The incoming request
     * @return Request The processed request with user data
     * @throws HttpException When authentication fails
     */
    public function handle(Request $request): Request
    {
        $authHeader = $request->header('Authorization');

        if (empty($authHeader)) {
            throw new HttpException('Authorization header is required', 401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            throw new HttpException('Invalid authorization format, use Bearer token', 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_KEY'], 'HS256'));

            $request->setAuthUser($decoded->data);

            return $request;
        } catch (ExpiredException $e) {
            throw new HttpException('Your session has expired, please login again', 401);
        } catch (SignatureInvalidException $e) {
            throw new HttpException('Invalid authentication token', 401);
        } catch (Throwable $e) {
            $message = $_ENV['APP_ENV'] === 'production'
                ? 'Authentication failed. Please try again or contact support.'
                : 'Authentication error: ' . $e->getMessage();

            throw new HttpException($message, 500);
        }
    }
}
