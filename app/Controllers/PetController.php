<?php

declare(strict_types=1);

namespace App\Controllers;

use Exception;
use App\Response;
use App\Models\Pet;
use App\Controllers\Controller;
use App\Exceptions\HttpException;

class PetController extends Controller
{
    public function index(): Response
    {
        try {
            $pets = Pet::getAll();

            return view('json',  $pets, 200);
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), 500, $e);
        }
    }
}
