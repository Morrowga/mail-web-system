<?php

namespace App\Repositories;

use App\Models\Template;
use Illuminate\Http\Request;
use App\Traits\CRUDResponses;
use App\Models\TemplateCategory;
use Illuminate\Support\Facades\DB;
use App\Interfaces\TemplateRepositoryInterface;

class TemplateRepository implements TemplateRepositoryInterface
{
    use CRUDResponses;

    public function index()
    {
        try {

            $templates = TemplateCategory::with('templates')->get();

            return $this->success('Fetched Templates', $templates);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }

    public function getOnlyTemplates()
    {
        try {

            $templates = Template::get();

            return $this->success('Fetched Templates', $templates);

        } catch (\Exception $e) {

            return $this->error($e->getMessage());

        }

    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $category = Template::create($request->all());

            DB::commit();

            return $this->success('Template has been created successfully.');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function update(Request $request, Template $template)
    {
        DB::beginTransaction();

        try {
            if($template)
            {
                $template->update($request->all());

                DB::commit();

                return $this->success('Template has been updated successfully.');

            }

            return $this->error('Data not found');

        } catch (\Exception $e) {
            DB::rollback();

            return $this->error($e->getMessage());
        }
    }

    public function delete(Template $template)
    {
        try {
            if($template)
            {
                $template->delete();
            }

            return $this->success('Template has been deleted');

        } catch (\Exception $e) {

            return $this->error($e->getMessage());
        }
    }
}
