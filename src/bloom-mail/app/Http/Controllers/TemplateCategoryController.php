<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\TemplateCategory;
use App\Http\Requests\TemplateCategoryRequest;
use App\Interfaces\TemplateCategoryRepositoryInterface;

class TemplateCategoryController extends Controller
{

    private TemplateCategoryRepositoryInterface $templateCategoryRepository;

    public function __construct(TemplateCategoryRepositoryInterface $templateCategoryRepository)
    {
        $this->templateCategoryRepository = $templateCategoryRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_user_permission('templatecategory_read')) {
            return abort(401);
        }

        $templateCategories = $this->templateCategoryRepository->index();

        return Inertia::render('TemplateCategories/Index', [
            "template_categories" => $templateCategories['data']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('templatecategory_createdit')) {
            return abort(401);
        }

        return Inertia::render('TemplateCategories/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateCategoryRequest $request)
    {
        $createTemplateCategory = $this->templateCategoryRepository->store($request);

        return redirect()->route('template-categories.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemplateCategory $template_category)
    {
        if (!check_user_permission('templatecategory_createdit')) {
            return abort(401);
        }

        return Inertia::render('TemplateCategories/CreateEdit', [
            "template_category" => $template_category
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TemplateCategoryRequest $request, TemplateCategory $template_category)
    {
        if (!check_user_permission('templatecategory_createdit')) {
            return abort(401);
        }

        $updateCategory = $this->templateCategoryRepository->update($request, $template_category);

        return redirect()->route('template-categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemplateCategory $template_category)
    {
        if (!check_user_permission('templatecategory_delete')) {
            return abort(401);
        }

        $deleteCategory = $this->templateCategoryRepository->delete($template_category);

        return redirect()->back();
    }
}
