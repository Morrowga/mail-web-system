<?php

namespace App\Repositories;

use App\Models\Folder;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Models\FolderAdvanceSearch;
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
            $folder = Folder::create($request->all());

            $extra_searches = $request->extra_search;
            if (is_array($extra_searches) && count($extra_searches) > 0) {
                foreach ($extra_searches as &$extra_search) {
                    $extra_search['folder_id'] = $folder->id;
                }
                FolderAdvanceSearch::insert($extra_searches);
            }

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
                $folder->load('extra_searches');
                $folder->extra_searches()->delete();
                $folder->update($request->all());
                $extra_searches = $request->extra_search ?? [];
                $purifiedArray = [];
                if (is_array($extra_searches) && count($extra_searches) > 0) {
                    foreach ($extra_searches as &$extra_search) {
                        array_push($purifiedArray, [
                            "search_character" => $extra_search['search_character'],
                            "method" => $extra_search['method'],
                            "is_exclude" => $extra_search['is_exclude'],
                            "folder_id" => $folder->id
                        ]);
                    }
                    FolderAdvanceSearch::insert($purifiedArray);
                }

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
