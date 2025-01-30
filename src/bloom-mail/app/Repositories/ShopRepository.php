<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Interfaces\ShopRepositoryInterface;

class ShopRepository implements ShopRepositoryInterface
{
    use CRUDResponses;

    public function index(Request $request)
    {
        try {

            $shops = Shop::orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

            return $this->success('Fetched Shops', $shops);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $shops = Shop::create($request->all());

            DB::commit();

            return $this->success('Shop has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Shop $shop)
    {
        DB::beginTransaction();

        try {
            if($shop)
            {
                $shop->update($request->all());

                DB::commit();

                return $this->success('Shop has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Shop $shop)
    {
        try {
            if($shop)
            {
                $shop->delete();
            }

            return $this->success('Shop has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
