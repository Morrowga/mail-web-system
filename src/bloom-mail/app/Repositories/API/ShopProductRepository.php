<?php

namespace App\Repositories\API;

use App\Models\Shop;
use App\Models\Product;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ShopResource;
use App\Http\Resources\ProductResource;
use App\Interfaces\API\ShopProductRepositoryInterface;

class ShopProductRepository implements ShopProductRepositoryInterface
{
    use ApiResponses;

    public function index(Request $request)
    {
        try {

            $shops = Shop::with(['products' => function ($query) {
                $query->where('status', 'release');
            }])
            ->where('status', 'release')
            ->orderBy('created_at', 'desc')
            ->paginate($request->query('per_page') ?? 10);

            $shopsArray = [
                'current_page' => $shops->currentPage(),
                'data' => ShopResource::collection($shops),
                'total' => $shops->total(),
                'per_page' => $shops->perPage(),
                'last_page' => $shops->lastPage(),
                'from' => $shops->firstItem(),
                'to' => $shops->lastItem(),
            ];


            return $this->success('Fetched Stores', $shopsArray);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }
}
