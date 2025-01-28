<?php

namespace App\Interfaces\API;

use Illuminate\Http\Request;

interface AuthRepositoryInterface
{
    public function registration(Request $request);

    public function login(Request $request);

}
