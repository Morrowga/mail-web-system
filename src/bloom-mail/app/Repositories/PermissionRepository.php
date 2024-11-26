<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Interfaces\PermissionRepositoryInterface;

class PermissionRepository implements PermissionRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $permissions = Permission::paginate(10);

            return $this->success('Fetched Permissions', $permissions);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $role = Permission::create($request->all());

            DB::commit();

            return $this->success('Permission has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Permission $permission)
    {
        DB::beginTransaction();

        try {
            if($permission)
            {
                $permission->update($request->all());

                DB::commit();

                return $this->success('Permission has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Permission $permission)
    {
        try {
            if($permission)
            {
                $permission->delete();
            }

            return $this->success('Permission has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
