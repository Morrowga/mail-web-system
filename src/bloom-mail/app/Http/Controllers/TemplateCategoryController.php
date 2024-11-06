<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
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
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateCategoryRequest $request)
    {
        $createTemplateCategory = $this->templateCategoryRepository->store($request);

        return redirect()->back()->with('success', 'Form submitted successfully');
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
