<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'templates';

    protected $fillable = ['title', 'subject', 'message_content', 'template_category_id'];


    public function templateCategory()
    {
        return $this->belongsTo(TemplateCategory::class);
    }
}
