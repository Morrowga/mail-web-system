<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use App\Models\TemplateCategory;
use Illuminate\Support\Facades\DB;
use App\Interfaces\TemplateCategoryRepositoryInterface;

class TemplateCategoryRepository implements TemplateCategoryRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $categories = TemplateCategory::get();

            return $this->success('Fetched Template Categories', $categories);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $category = TemplateCategory::create($request->all());

            DB::commit();

            return $this->success('Template Category has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    // public function update(Request $request, TemplateCategory $template_category)
    // {
    //     DB::beginTransaction();

    //     try {
    //         if($template_category)
    //         {
    //             $template_category->update($request->all());

    //             DB::commit();

    //             return $this->success('Template Category has been updated successfully.');

    //         }

    //         return $this->error('Data not found');

    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         return $this->error($e->getMessage());
    //     }
    // }

    // public function delete(Category $category)
    // {
    //     try {
    //         if($category)
    //         {
    //             $category->delete();
    //         }

    //         return $this->success('Category has been deleted');

    //     } catch (\Exception $e) {

    //         return $this->error($e->getMessage());
    //     }
    // }
}
