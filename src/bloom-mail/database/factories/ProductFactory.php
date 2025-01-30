<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'shop_id' => Shop::inRandomOrder()->first()?->id ?? Shop::factory(),
            'treatment_begin_date' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'product_detail' => $this->faker->paragraph,
            'content_time_frame' => $this->faker->randomNumber(3, true),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'sale_start_date' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'sale_end_date' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'status' => $this->faker->randomElement(['release']),
            'purchase_no' => $this->faker->unique()->numerify('###########'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
