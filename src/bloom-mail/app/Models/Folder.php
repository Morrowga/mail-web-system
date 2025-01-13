<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $table = 'folders';

    protected $fillable = ['search_character', 'method', 'name'];

    public function mails()
    {
        return $this->belongsToMany(MailLog::class, 'folder_mails', 'folder_id', 'mail_log_id');
    }

    public function extra_searches()
    {
        return $this->hasMany(FolderAdvanceSearch::class, 'folder_id');
    }
}
