<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'name'               => ucfirst($name),
            'slug'               => Str::slug($name . '-' . Str::random(5)),
            'sku'                => strtoupper(Str::random(10)),
            'price'              => $this->faker->randomFloat(2, 5, 199),
            'compare_at_price'   => $this->faker->boolean(30) ? $this->faker->randomFloat(2, 10, 249) : null,
            'stock'              => $this->faker->numberBetween(0, 100),
            'is_active'          => true,
            'is_featured'        => $this->faker->boolean(10),
            'short_description'  => $this->faker->sentence(10),
            'description'        => $this->faker->paragraph(4),
            'weight'             => $this->faker->boolean(40) ? $this->faker->randomFloat(3, 0.1, 3) : null,
        ];
    }
}
