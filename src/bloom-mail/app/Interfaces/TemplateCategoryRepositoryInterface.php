<?php

namespace App\Interfaces;

use App\Models\Category;
use Illuminate\Http\Request;

interface TemplateCategoryRepositoryInterface
{
    public function index();

    public function store(Request $request);
}
