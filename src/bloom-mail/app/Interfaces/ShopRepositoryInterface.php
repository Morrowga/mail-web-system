<?php

namespace App\Interfaces;

use App\Models\Shop;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

interface ShopRepositoryInterface
{
    public function index(Request $request);

    public function store(Request $request);

    public function update(Request $request, Shop $shop);

    public function delete(Shop $shop);
}
