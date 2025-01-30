<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use App\Interfaces\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    use CRUDResponses;

    public function index(Request $request)
    {
        try {

            $products = Product::orderBy('created_at', 'desc')->paginate($request->per_page ?? 10);

            return $this->success('Fetched Products', $products);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->all());

            DB::commit();

            return $this->success('Product has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
        DB::beginTransaction();

        try {
            if($product)
            {
                $product->update($request->all());

                DB::commit();

                return $this->success('Product has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Product $product)
    {
        try {
            if($product)
            {
                $product->delete();
            }

            return $this->success('Product has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
