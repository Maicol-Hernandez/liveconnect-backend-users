<?php

namespace App\Middleware;

use App\Request;

abstract class Middleware
{
    abstract public function handle(Request $request): Request;
}
