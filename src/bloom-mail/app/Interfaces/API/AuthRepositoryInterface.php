<?php

namespace App\Interfaces\API;

use Illuminate\Http\Request;

interface AuthRepositoryInterface
{
    public function registration(Request $request);

    public function login(Request $request);

    public function logout(Request $request);

    public function revokeToken(Request $request);
}
