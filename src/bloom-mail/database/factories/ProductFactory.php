<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'shop_name' => $this->faker->company, // Dummy shop name
            'treatment_begin_date' => $this->faker->date(), // Random date for treatment begin
            'product_detail' => $this->faker->paragraph, // Random text for product details
            'price' => $this->faker->randomFloat(2, 10, 1000), // Random price between 10 and 1000
            'sale_start_date' => $this->faker->date(), // Random sale start date
            'sale_end_date' => $this->faker->date(), // Random sale end date
            'status' => $this->faker->randomElement(['release']), // Random status
            'purchase_no' => $this->faker->unique()->numerify('###########'), // 11-digit unique purchase number
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
