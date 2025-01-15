<?php

namespace App\Repositories;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\FolderRepositoryInterface;

class FolderRepository implements FolderRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $folders = Folder::get();

            return $this->success('Fetched Folders', $folders);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $category = Folder::create($request->all());

            DB::commit();

            return $this->success('Folder has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Folder $folder)
    {
        DB::beginTransaction();

        try {
            if($folder)
            {
                $folder->update($request->all());

                DB::commit();

                return $this->success('folder has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Folder $folder)
    {
        try {
            if($folder)
            {
                $folder->delete();
            }

            return $this->success('Folder has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
