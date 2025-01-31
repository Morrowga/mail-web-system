<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory, UUID;

    protected $table = 'shops';

    protected $fillable = [
        'name',
        'status',
        'shop_type',
        'address',
        'opening_time',
        'closing_time',
        'phone_no',
        'reception_start_time',
        'reception_end_time',
        'close_day',
        'room_numbers',
        'close_day_text',
        'access',
        'parking_nearby',
        'store_direction',
        'gmap_location',
        'gmap_photos',
        'youtube',
        'top_statement',
        'sub_title',
        'btm_text',
        'sub_title_two',
        'btm_text_two'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
