<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingMethod;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // 5 categorie
        $categories = [
            ['name' => 'Caffetteria', 'slug' => 'caffetteria'],
            ['name' => 'Pasticceria', 'slug' => 'pasticceria'],
            ['name' => 'Confetteria', 'slug' => 'confetteria'],
            ['name' => 'Gift Box', 'slug' => 'gift-box'],
            ['name' => 'Accessori', 'slug' => 'accessori'],
        ];
        Category::insert(array_map(fn($c) => [
            ...$c,
            'created_at' => now(),
            'updated_at' => now(),
        ], $categories));

        // Prodotti faker (20 prodotti random)
        Product::factory(20)->create();

        // 1 metodo spedizione
        ShippingMethod::create([
            'name'      => 'Standard',
            'rate'      => 6.90,
            'is_active' => true,
        ]);
    }
}
