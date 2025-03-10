<?php

use App\Router;
use App\Middleware\AdminAuth;
use App\Middleware\BasicAuth;
use App\Controllers\PetController;
use App\Controllers\AuthController;
use App\Controllers\UserController;


Router::get('/', function () {
    return view('json', 'Hello world api', 200);
});

// user
Router::get('/api/v1/users', UserController::class . '@index', BasicAuth::class); //, AdminAuth::class
Router::post('/api/v1/users', UserController::class . '@store');
Router::get('/api/v1/users/(?<id>\d+)', UserController::class . '@show', BasicAuth::class);
Router::put('/api/v1/users/(?<id>\d+)', UserController::class . '@update', BasicAuth::class);
Router::delete('/api/v1/users/(?<id>\d+)', UserController::class . '@destroy', BasicAuth::class); //, AdminAuth::class

// Pets
Router::get('/api/v1/pets', PetController::class . '@index');

// login
Router::post('/api/v1/login', AuthController::class . '@login');
// Register
Router::post('/api/v1/register', AuthController::class . '@register');
// logout
Router::post('/api/v1/logout', AuthController::class . '@logout', BasicAuth::class);
