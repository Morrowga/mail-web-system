<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Template;
use Illuminate\Http\Request;
use App\Http\Requests\TemplateRequest;
use App\Interfaces\FolderRepositoryInterface;
use App\Interfaces\TemplateRepositoryInterface;
use App\Interfaces\TemplateCategoryRepositoryInterface;

class TemplateController extends Controller
{
    private TemplateRepositoryInterface $templateRepository;

    public function __construct(
        TemplateRepositoryInterface $templateRepository,
        TemplateCategoryRepositoryInterface $templateCategoryRepository,
        FolderRepositoryInterface $folderRepository,
    )
    {
        $this->templateRepository = $templateRepository;
        $this->templateCategoryRepository = $templateCategoryRepository;
        $this->folderRepository = $folderRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!check_user_permission('template_read')) {
            return abort(401);
        }

        $templates = $this->templateRepository->index();

        return Inertia::render('Templates/Index', [
            "template_categories" => $templates['data'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!check_user_permission('template_createdit')) {
            return abort(401);
        }

        $template_categories = $this->templateCategoryRepository->index();
        $folders = $this->folderRepository->index();

        return Inertia::render('Templates/CreateEdit', [
            "template_categories" => $template_categories['data'],
            "folders" => $folders['data'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TemplateRequest $request)
    {
        if (!check_user_permission('template_createdit')) {
            return abort(401);
        }

        $createTemplate = $this->templateRepository->store($request);

        return redirect()->route('templates.index');
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
    public function edit(Template $template)
    {
        if (!check_user_permission('template_createdit')) {
            return abort(401);
        }

        $template_categories = $this->templateCategoryRepository->index();
        $folders = $this->folderRepository->index();

        return Inertia::render('Templates/CreateEdit', [
            "template" => $template,
            "template_categories" => $template_categories['data'],
            "folders" => $folders['data'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Template $template)
    {
        if (!check_user_permission('template_createdit')) {
            return abort(401);
        }

        $updateTemplate = $this->templateRepository->update($request, $template);

        return redirect()->route('templates.index')->with('success', 'Form submitted successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Template $template)
    {
        if (!check_user_permission('template_delete')) {
            return abort(401);
        }

        $deleteTemplate = $this->templateRepository->delete($template);

        return redirect()->back();
    }
}
