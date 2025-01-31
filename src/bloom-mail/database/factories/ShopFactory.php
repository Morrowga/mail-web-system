<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    protected $model = Shop::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'status' => $this->faker->randomElement(['release']),
            'shop_type' => $this->faker->randomElement(['type1', 'type2', 'type3']),
            'address' => $this->faker->address,
            'opening_time' => $this->faker->time('H:i'),
            'closing_time' => $this->faker->time('H:i'),
            'phone_no' => $this->faker->phoneNumber,
            'reception_start_time' => $this->faker->time('H:i'),
            'reception_end_time' => $this->faker->time('H:i'),
            'close_day' => $this->faker->randomElement(['Sunday', 'Monday', 'Tuesday']),
            'room_numbers' => $this->faker->numberBetween(1, 20),
            'close_day_text' => $this->faker->sentence,
            'access' => $this->faker->text(200),
            'parking_nearby' => $this->faker->boolean,
            'store_direction' => $this->faker->text(200),
            'gmap_location' => $this->faker->url,
            'gmap_photos' => json_encode([$this->faker->imageUrl()]),
            'youtube' => $this->faker->url,
            'top_statement' => $this->faker->paragraph,
            'store_sub_title' => $this->faker->sentence,
            'store_btm_text' => $this->faker->sentence,
            'store_sub_title_two' => $this->faker->sentence,
            'store_btm_text_two' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
