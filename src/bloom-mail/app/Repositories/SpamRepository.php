<?php

namespace App\Repositories;

use App\Models\Spam;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use App\Interfaces\SpamRepositoryInterface;

class SpamRepository implements SpamRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $spams = Spam::get();

            return $this->success('Fetched Spams', $spams);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $category = Spam::create($request->all());

            DB::commit();

            return $this->success('Spam has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Spam $spam)
    {
        DB::beginTransaction();

        try {
            if($spam)
            {
                $spam->update($request->all());

                DB::commit();

                return $this->success('Spam has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Spam $spam)
    {
        try {
            if($spam)
            {
                $spam->delete();
            }

            return $this->success('Spam has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
