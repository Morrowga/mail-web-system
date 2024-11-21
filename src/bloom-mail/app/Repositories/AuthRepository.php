<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\AuthRepositoryInterface;

class AuthRepository implements AuthRepositoryInterface
{
    use CRUDResponses;

    public function accountRegistration(Request $request)
    {
        try {
            $user = User::create([
                "name" => $request->name,
                "login_id" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            return $this->success('User is created', $user);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }
    }

}
