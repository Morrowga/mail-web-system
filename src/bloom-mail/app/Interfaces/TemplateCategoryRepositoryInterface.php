<?php

namespace App\Interfaces;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\TemplateCategory;

interface TemplateCategoryRepositoryInterface
{
    public function index();

    public function store(Request $request);

    public function update(Request $request, TemplateCategory $template_category);

    public function delete(TemplateCategory $template_category);
}
