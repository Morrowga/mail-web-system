<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory, UUID;

    protected $table = 'members';

    protected $fillable = ['dob', 'user_id','age', 'user_id', 'phone'];
}
