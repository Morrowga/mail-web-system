<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    use CRUDResponses;


    public function index()
    {
        try {

            $currentUserRole = Auth::user()->role_id;

            $query = User::query();

            $users = $currentUserRole == 1 ? $query->paginate(10) : $query->where('id', '!=',  1)->paginate(10);

            return $this->success('Fetched Users', $users);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::create([
                "name" => $request->name,
                "login_id" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);

            $user->assignRole(Role::where('id', $request->role_id)->first()->name);

            return $this->success('User is created', $user);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }
    }

    public function update(Request $request, User $user)
    {
        DB::beginTransaction();


        try {
            if($user)
            {
                $user->update([
                    "name" => $request->name,
                    "email" => $request->email
                ]);

                $user->assignRole(Role::where('id', $request->role_id)->first()->name);

                DB::commit();

                return $this->success('User has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(User $user)
    {
        try {
            if($user)
            {
                $user->delete();
            }

            return $this->success('User has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }

}
