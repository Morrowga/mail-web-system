<?php

namespace App\Models;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = ['message_id', 'status', 'body', 'sender', 'subject','parent_message_id','name','uid', 'datetime'];
    
    public function allReplies()
    {
        return $this->hasMany(MailLog::class, 'parent_message_id', 'message_id')
                    ->with('allReplies');  // Recursive relationship to retrieve entire thread
    }


}
