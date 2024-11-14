<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentMail extends Model
{
    protected $table = 'sent_mails';

    protected $fillable = ['message_id', 'body', 'sender', 'subject','name','uid', 'datetime', 'type', 'parent_id'];

    public function getNameAttribute($value)
    {
        return iconv_mime_decode($value);
    }
}
