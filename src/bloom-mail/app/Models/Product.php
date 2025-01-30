<?php

namespace App\Models;

use App\Models\Shop;
use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, UUID;

    protected $table = 'products';

    protected $fillable = ['shop_id', 'treatment_begin_date','content_time_frame', 'product_detail', 'price', 'sale_start_date', 'sale_end_date', 'status', 'purchase_no'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
