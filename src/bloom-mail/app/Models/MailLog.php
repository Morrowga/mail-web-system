<?php

namespace App\Models;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = ['message_id', 'status', 'body', 'sender', 'subject','name','uid', 'datetime', 'previous_status'];


    public function mail_histories()
    {
        return $this->hasMany(SentMail::class, 'parent_id');
    }

    public function getSubjectAttribute($value)
    {
        try {
            $decodedValue = iconv_mime_decode($value, 0, 'UTF-8');
            return $decodedValue ?: $value;
        } catch (\Exception $e) {
            logger()->error("Error decoding subject: " . $e->getMessage());
            return $value;
        }
    }

    public function getNameAttribute($value)
    {
        try {
            $decodedValue = iconv_mime_decode($value, 0, 'UTF-8');
            return $decodedValue ?: $value;
        } catch (\Exception $e) {
            logger()->error("Error decoding subject: " . $e->getMessage());
            return $value;
        }
    }
}
