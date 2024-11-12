<?php

namespace App\Models;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = ['message_id', 'status', 'body', 'sender', 'subject','name','uid', 'datetime'];

}
