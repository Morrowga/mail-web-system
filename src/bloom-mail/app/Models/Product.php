<?php

namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, UUID;

    protected $table = 'products';

    protected $appends = ['images'];

    protected $fillable = ['shop_name', 'treatment_begin_date','product_detail', 'price', 'sale_start_date', 'sale_end_date', 'status', 'purchase_no'];

    public function getImagesAttribute()
    {
        return [
            "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpyus1bvneCaCYSgG-69eaJQnDYZtlztupUa8utc6gDWM9NCLBGL4cBJGpix7BJQruyCs&usqp=CAU",
            "https://gawdai.com/sharedgawdai/gawdai-746.jpg"
        ];
    }

}
