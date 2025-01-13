<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FolderAdvanceSearch extends Model
{
    protected $fillable = ['search_character', 'folder_id', 'method', 'folder_id', 'is_exclude'];

    protected $table = 'folder_advance_searches';

    protected $casts = [
        'is_exclude' => 'boolean',
    ];
}
