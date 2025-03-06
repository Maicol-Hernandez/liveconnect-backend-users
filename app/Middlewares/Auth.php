<?php

namespace App\Middlewares;

use Api\Maicoldev\Exceptions\HttpException;
use Api\Maicoldev\Request;

class Auth
{

    public static function isAuth(Request $request): void
    {
        if ($request->getData('user_id')) {
            # error 401 Unauthorized
            throw new HttpException("You must be login", 401);
        }
    }
}
