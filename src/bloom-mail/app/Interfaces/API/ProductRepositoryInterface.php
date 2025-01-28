<?php

namespace App\Interfaces\API;

use Illuminate\Http\Request;

interface ProductRepositoryInterface
{
    public function index(Request $request);
}
