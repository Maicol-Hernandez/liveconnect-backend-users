<?php

namespace App\Controllers;

use Exception;
use App\Request;
use App\Response;
use App\Models\Pet;
use App\Models\User;
use App\Middleware\Auth;
use App\Database\Connection;
use App\Validation\Validator;
use App\Controllers\Controller;
use App\Exceptions\HttpException;
use App\Models\PetUser;

class UserController extends Controller
{

    public function index(): Response
    {
        try {
            $users = User::getAll();

            return view('json',  $users, 200);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }

    public function store(Request $request): Response
    {
        Connection::getInstance()->beginTransaction();
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
            PetUser::createBulk($user['id'], $request->input('pets'));

            Connection::getInstance()->commit();

            return view('json', $user, 201);
        } catch (HttpException $e) {
            Connection::getInstance()->rollback();
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }

    public function show(int $id): Response
    {
        return view('json', User::findById($id), 200);
    }

    public function update(int $id, Request $request): Response
    {
        Connection::getInstance()->beginTransaction();
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

            $user = User::update($id, $userData);
            PetUser::updateBulk($user['id'], $request->input('pets'));

            Connection::getInstance()->commit();

            return view('json', $user, 200);
        } catch (HttpException $e) {
            Connection::getInstance()->rollback();
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }

    public function destroy(int $id): Response
    {
        User::delete($id);

        return view('json', 'User deleted', 200);
    }
}
