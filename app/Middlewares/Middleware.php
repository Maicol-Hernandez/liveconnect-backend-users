<?php

namespace Api\Maicoldev\middlewares;

use Api\Maicoldev\Request;


abstract class Middleware
{

    abstract public function handle(Request $request): Request;
}
