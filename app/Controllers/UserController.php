<?php

namespace App\Controllers;

use Exception;
use App\Request;
use App\Response;
use App\Models\User;
use App\Middleware\Auth;
use App\Validation\Validator;
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

    public function store(Request $request): Response
    {
        try {
            $validator = new Validator($request->all(), [
                'name' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'password'],
                "pets" => ['required', 'array'],
            ]);

            $validator->validate();

            $userData = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
            ];

            $user = User::create($userData);

            return view('json', ['status' => 'success', 'data' => $user], 201);
        } catch (HttpException $e) {
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }

    public function show(int $id, Request $request): Response
    {
        // Auth::isAuth($request);
        // $user_id = $request->getData('user_id');
        return view('json', User::findById($id));
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
