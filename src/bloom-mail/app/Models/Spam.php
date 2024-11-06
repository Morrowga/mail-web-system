<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spam extends Model
{
    protected $table = 'spam_mails';

    protected $fillable = ['mail_address'];
}
