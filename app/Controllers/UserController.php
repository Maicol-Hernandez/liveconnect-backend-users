<?php

namespace App\Controllers;

use Exception;
use App\Request;
use App\Response;
use App\Models\User;
use App\Middleware\Auth;
use App\helpers\Pagination;
use App\Controllers\Controller;
use App\Exceptions\HttpException;


class UserController extends Controller
{

    public function index(): Response
    {
        try {
            $users = User::getAll();

            return view('json', ['status' => 'success', 'data' => $users], 200);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }

    public function store(): Response
    {
        $fields = ['name', 'username', 'email', 'password', 'is_active', 'is_admin'];

        foreach ($fields as $field) {
            # all fiels data
            if (!isset($_POST[$field])) {
                # error 400 Bad request
                throw new HttpException("You must send field {$field}", 400);
                exit;
            }
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            # error 422 Payment required
            throw new HttpException("Invalid 'email' format", 422);
            exit;
        }

        if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 64) {
            # error 422 Payment required
            throw new HttpException("The 'password' field must be between 6 and 64 characters long", 422);
            exit;
        }

        // echo "is_active controller ",$_POST['is_active'] . "\n";

        $user = new User($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password'], $_POST['is_active'], $_POST['is_admin']);



        return view('json', "User created succesfulley, id user {$user->create()}", 201); // Satisfactoria 201 Create 
    }

    public function show(int $id, Request $request): Response
    {
        // Auth::isAuth($request);
        // $user_id = $request->getData('user_id');
        return view('json', User::getUserId($id));
    }

    public function update(Request $request): Response
    {
        Auth::isAuth($request);
        return view('json', []);
    }

    public function destroy(int $id, Request $request): Response
    {
        echo "user id ", $id, "\n";
        return view('raw', 'delete'); // 204 Not Content
    }
}
