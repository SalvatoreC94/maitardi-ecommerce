<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ShippingMethod;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Admin demo ---
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // --- 5 categorie ---
        $categories = [
            ['name' => 'Caffetteria', 'slug' => 'caffetteria'],
            ['name' => 'Pasticceria', 'slug' => 'pasticceria'],
            ['name' => 'Confetteria', 'slug' => 'confetteria'],
            ['name' => 'Gift Box',    'slug' => 'gift-box'],
            ['name' => 'Accessori',   'slug' => 'accessori'],
        ];
        Category::insert(array_map(fn($c) => [
            ...$c,
            'created_at' => now(),
            'updated_at' => now(),
        ], $categories));

        // --- 1 solo metodo di spedizione ---
        ShippingMethod::create([
            'name'      => 'Standard',
            'rate'      => 6.90,
            'is_active' => true,
            'min_total' => null,
            'max_total' => null,
        ]);

        // --- Prodotti demo (~4 per categoria) + immagine primaria fittizia ---
        if (Product::count() === 0) {
            $categoryIds = Category::pluck('id')->all();

            foreach ($categoryIds as $catId) {
                Product::factory()
                    ->count(4)
                    ->create(['category_id' => $catId])
                    ->each(function (Product $product) {
                        // immagine placeholder (sostituisci con upload reali in futuro)
                        $product->images()->create([
                            'path'       => 'products/demo/'.Str::random(12).'.jpg',
                            'alt'        => $product->name,
                            'is_primary' => true,
                            'sort'       => 0,
                        ]);
                    });
            }
        }

        // --- Ordini demo (3 ordini, 2 righe ciascuno) ---
        $productsPool = Product::inRandomOrder()->take(5)->get();

        for ($i = 1; $i <= 3; $i++) {
            $subtotal = 0;

            $order = Order::create([
                'code'            => 'ORD-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'user_id'         => null, // guest
                'status'          => 'pending',

                'subtotal'        => 0,    // aggiorno dopo
                'shipping_cost'   => 6.90,
                'discount_total'  => 0,
                'tax_total'       => 0,
                'total'           => 0,    // aggiorno dopo

                'payment_method'  => 'test',
                'payment_status'  => 'pending',

                'customer_email'  => "cliente{$i}@example.com",
                'customer_name'   => "Cliente Demo {$i}",
                'customer_phone'  => "123456789{$i}",

                'billing_address_json'  => [
                    'via'   => 'Via Demo ' . $i,
                    'citta' => 'Napoli',
                    'cap'   => '80100',
                ],
                'shipping_address_json' => [
                    'via'   => 'Via Spedizione ' . $i,
                    'citta' => 'Napoli',
                    'cap'   => '80100',
                ],

                'notes'           => null,
                'placed_at'       => now(),
            ]);

            foreach ($productsPool->random(2) as $product) {
                $qty       = rand(1, 3);
                $price     = $product->price;
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'total'      => $lineTotal,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total'    => $subtotal + $order->shipping_cost,
            ]);
        }
    }
}
