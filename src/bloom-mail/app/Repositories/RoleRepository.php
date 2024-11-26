<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Interfaces\RoleRepositoryInterface;

class RoleRepository implements RoleRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $roles = Role::paginate(10);

            return $this->success('Fetched Roles', $roles);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $role = Role::create($request->all());

            DB::commit();

            return $this->success('Role has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Role $role)
    {
        DB::beginTransaction();

        try {
            if($role)
            {
                $role->update($request->all());

                DB::commit();

                return $this->success('Role has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Role $role)
    {
        try {
            if($role)
            {
                $role->delete();
            }

            return $this->success('Role has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
