<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $fillable = ['file_name','mime_type', 'path', 'file_size', 'mail_log_id'];
}
