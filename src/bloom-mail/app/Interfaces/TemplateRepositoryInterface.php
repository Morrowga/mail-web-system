<?php

namespace App\Interfaces;

use App\Models\Template;
use Illuminate\Http\Request;

interface TemplateRepositoryInterface
{
    public function index();

    public function getOnlyTemplates();

    public function store(Request $request);

    public function update(Request $request, Template $template);

    public function delete(Template $template);
}
