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
        DB::beginTransaction();

        try {

            $user = User::create([
                'name' => $request->name,
                'login_id' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('User');

            $token = JWTAuth::fromUser($user);

            DB::commit();

            return $this->success('Your Registration successfully created.', new UserResource($user), $token);

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {

        try {
            $credentials = $request->only('email' ,'password');

            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Your crendentials are wrong'], 400);
            }

            $user = auth()->user();

            $token = JWTAuth::claims(['role' => $user->role_name])->fromUser($user);

            return $this->success('User successfully logged.', new UserResource($user), $token);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return $this->error('No token provided.');
        }

        try {
            JWTAuth::invalidate($token);

            return $this->success('User successfully logged out.', []);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function forgetPassword(Request $request)
    {
        $email = $request->email;

        try {
            $user = User::where('email', $email)->first();



            return $this->success('User successfully logged out.', []);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function revokeToken(Request $request)
    {
        $token = JWTAuth::getToken();

        if (!$token) {
            return response()->json(['message' => 'Token not found'], 400);
        }

        try {
            JWTAuth::invalidate($token);

            $user = JWTAuth::user();

            $newToken = JWTAuth::fromUser($user);

            return $this->success('Token revoked and renewed successfully.', [], $newToken);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error('Token cannot revoke.', 500);
        }
    }
}
