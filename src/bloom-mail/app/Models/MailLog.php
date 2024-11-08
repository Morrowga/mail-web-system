<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = ['message_id', 'status', 'datetime', 'subject', 'sender', 'name', 'body', 'parent_id'];

    public function replies()
    {
        return $this->hasMany(MailLog::class, 'parent_id');
    }
}
