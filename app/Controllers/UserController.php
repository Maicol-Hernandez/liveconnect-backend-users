<?php

namespace App\Controllers;

use App\Controllers\Controller;
use Api\Maicoldev\Exceptions\HttpException;
use App\Models\User;
use Api\Maicoldev\Request;
use App\Middlewares\Auth;

use App\helpers\Pagination;


class UserController extends Controller
{

    /**
     * 
     */
    public function create()
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

    /**
     * @return object Users
     */
    public function all()
    {
        $users = User::getAll();
        foreach ($users as &$user) {
            unset($user['password']);
        }
        unset($user);

        $fields = ['is_active', 'is_admin'];

        return view('json', Pagination::pagination($users, $fields));
    }

    /**
     * 
     */
    public function show(int $id, Request $request)
    {

        // Auth::isAuth($request);

        // $user_id = $request->getData('user_id');


        return view('json', User::getUserId($id));
    }

    /**
     * 
     */
    public function edit(int $id)
    {
        return view('json', "edit {$id}", 200);
    }

    /**
     * 
     */
    public function update(Request $request)
    {
        Auth::isAuth($request);

        return view('json', []);
    }

    /**
     * 
     */
    public function delete(int $id, Request $request)
    {
        echo "user id ", $id, "\n";

        return view('raw', 'delete'); // 204 Not Content
    }
}
