<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Interfaces\MemberRepositoryInterface;

class MemberRepository implements MemberRepositoryInterface
{
    use CRUDResponses;


    public function index(Request $request)
    {
        try {

            $members = User::whereHas('roles', function ($query) {
                $query->where('name', 'User');
            })->with('member')->paginate($request->per_page ?? 10);


            return $this->success('Fetched Users', $members);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }
    }

    public function store(Request $request)
    {
        try {
            $user = User::create([
                "name" => $request->name,
                "name_kana" => $request->name_kana,
                "login_id" => $request->login_id,
                "email" => $request->email,
                "password" => Hash::make('123456'),
                "email_verified_at" => now()
            ]);

            $user->assignRole('User');

            $member = Member::create([
                "dob" => $request->dob,
                "age" => $request->age,
                "user_id" => $user->id,
                "phone" => $request->phone
            ]);

            return $this->success('Member is created', $member);

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
                $member = Member::where('user_id', $user->id)->first();
                if($member)
                {
                    $member->update($request->all());
                }

                $user->update($request->all());


                DB::commit();

                return $this->success('Member has been updated successfully.');

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
