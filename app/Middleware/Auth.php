<?php

namespace App\Middleware;

use App\Request;
use App\Exceptions\HttpException;

class Auth
{

    public static function isAuth(Request $request): void
    {
        if ($request->all('user_id')) {
            # error 401 Unauthorized
            throw new HttpException("You must be login", 401);
        }
    }
}
