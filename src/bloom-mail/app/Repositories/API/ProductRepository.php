<?php

namespace App\Repositories\API;

use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use App\Interfaces\API\ProductRepositoryInterface;

class ProductRepository implements ProductRepositoryInterface
{
    use ApiResponses;

    public function index(Request $request)
    {
        try {

            $products = Product::paginate($request->query('per_page') ?? 10);

            $productsArray = [
                'current_page' => $products->currentPage(),
                'data' => ProductResource::collection($products),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ];


            return $this->success('Fetched Products', $productsArray);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }
}
