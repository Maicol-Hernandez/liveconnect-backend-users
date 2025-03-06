<?php

use App\Router;
use App\Middleware\AdminAuth;
use App\Middleware\BasicAuth;
use App\Controllers\AuthController;
use App\Controllers\UserController;


Router::get('/', function () {
    return view('json', 'Hello world api', 200);
});

// user
Router::get('/api/v1/users', UserController::class . '@all', AdminAuth::class);
Router::get('/api/v1/users/(?<id>\d+)', UserController::class . '@show');
Router::post('/api/v1/users', UserController::class . '@create');
Router::put('/api/v1/users/(?<id>\d+)', UserController::class . '@update', BasicAuth::class);
Router::patch('/api/v1/users/(?<id>\d+)', UserController::class . '@edit', BasicAuth::class);
Router::delete('/api/v1/users/(?<id>\d+)', UserController::class . '@delete', AdminAuth::class);

// login
Router::post('/api/v1/login', AuthController::class . '@auth');

// Router::post('/users', function () {
//     return new \App\Response('json', 'User created succesfulley', 201);
// });