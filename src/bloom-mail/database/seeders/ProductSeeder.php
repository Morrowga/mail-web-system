<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shop::factory(20)->create();

        Product::factory(50)->create();
    }
}
