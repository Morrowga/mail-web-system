<?php

namespace App\Interfaces;

use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

interface ProductRepositoryInterface
{
    public function index(Request $request);

    public function store(Request $request);

    public function update(Request $request, Product $product);

    public function delete(Product $product);
}
