<?php

namespace App\Models;

use App\Models\MailLog;
use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = [
        'message_id',
        'status',
        'body',
        'sender',
        'subject',
        'name',
        'parent_id',
        'uid',
        'datetime',
        'previous_status',
        'person_in_charge',
        'deleted_at'
    ];

    public function mail_histories()
    {
        return $this->hasMany(SentMail::class, 'parent_id')->with('template');
    }

    public function mail_threads()
    {
        return $this->hasMany(MailLog::class, 'parent_id');
    }

    public function getSenderAttribute($value)
    {
        return $this->safeDecode($value, 'sender');
    }

    public function getSubjectAttribute($value)
    {
        return $this->safeDecode($value, 'subject');
    }

    public function getNameAttribute($value)
    {
        return $this->safeDecode($value, 'name');
    }

    private function safeDecode($value, $attribute)
    {
        try {
            if (preg_match('/=\?[^?]+\?/', $value)) {
                $decodedValue = iconv_mime_decode($value, 0, 'UTF-8');
                return $decodedValue ?: $value;
            }

            return $value;
        } catch (\Exception $e) {
            logger()->error("Error decoding {$attribute}: " . $e->getMessage());
            return $value;
        }
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'folder_mails', 'mail_log_id', 'folder_id');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
