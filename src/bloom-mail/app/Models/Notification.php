<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, UUID;

    protected $table = 'notifications';

    protected $fillable = ['title', 'content','start_time', 'end_time', 'type', 'status'];
}
