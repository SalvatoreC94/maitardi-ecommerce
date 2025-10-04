<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'name'        => ucfirst($name),
            'slug'        => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'sku'         => strtoupper(Str::random(8)),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->randomFloat(2, 1, 50),
            'stock'       => $this->faker->numberBetween(5, 50),
            'is_active'   => true,
        ];
    }
}
