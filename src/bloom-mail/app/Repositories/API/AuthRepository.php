<?php

namespace App\Repositories\API;

use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\API\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    use ApiResponses;

    public function registration(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'login_id' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('User');

        $token = JWTAuth::fromUser($user);

        return $this->success('Your Registration successfully created.', new UserResource($user), $token);
    }

    public function login(Request $request)
    {

        $credentials = $request->only('email' ,'password');

        if (! $token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Your crendentials are wrong'], 400);
        }

        $user = auth()->user();

        $token = JWTAuth::claims(['role' => $user->role_name])->fromUser($user);

        return $this->success('User successfully logged.', new UserResource($user), $token);
    }
}
