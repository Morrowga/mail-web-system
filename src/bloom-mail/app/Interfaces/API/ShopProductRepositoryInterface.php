<?php

namespace App\Interfaces\API;

use Illuminate\Http\Request;

interface ShopProductRepositoryInterface
{
    public function index(Request $request);
}
