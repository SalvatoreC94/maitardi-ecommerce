<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function generateOrderCode(): string
    {
        $seq = str_pad((string) (Order::max('id') + 1), 4, '0', STR_PAD_LEFT);
        return 'ORD-' . now()->format('Ymd') . '-' . $seq;
    }

    /**
     * Crea un ordine a partire dal carrello.
     * $payload: [
     *   'customer_email','customer_name','customer_phone'?,
     *   'billing' => ['via','citta','cap',...],
     *   'shipping'=> ['via','citta','cap',...],
     *   'shipping_method_id', 'notes'?
     * ]
     */
    public function createFromCart(\App\Models\Cart $cart, array $payload, ?int $userId = null): Order
    {
        if ($cart->items()->count() === 0) {
            throw new \RuntimeException('Il carrello è vuoto.');
        }

        $shipping = ShippingMethod::findOrFail($payload['shipping_method_id']);
        $subtotal = $cart->items->sum(fn($i) => $i->quantity * $i->unit_price);
        $shippingCost = $shipping->rate ?? 0;

        return DB::transaction(function () use ($cart, $payload, $userId, $subtotal, $shippingCost) {
            // Validazione stock
            foreach ($cart->items as $i) {
                if ($i->product->stock < $i->quantity) {
                    throw new \RuntimeException("Stock insufficiente per {$i->product->name}.");
                }
            }

            $order = Order::create([
                'code'                  => $this->generateOrderCode(),
                'user_id'               => $userId,
                'status'                => 'pending',
                'subtotal'              => $subtotal,
                'shipping_cost'         => $shippingCost,
                'discount_total'        => 0,
                'tax_total'             => 0,
                'total'                 => $subtotal + $shippingCost,
                'payment_method'        => 'test',
                'payment_status'        => 'pending',
                'customer_email'        => $payload['customer_email'],
                'customer_name'         => $payload['customer_name'],
                'customer_phone'        => $payload['customer_phone'] ?? null,
                'billing_address_json'  => $payload['billing'] ?? [],
                'shipping_address_json' => $payload['shipping'] ?? [],
                'notes'                 => $payload['notes'] ?? null,
                'placed_at'             => now(),
            ]);

            foreach ($cart->items as $i) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $i->product_id,
                    'sku'        => $i->product->sku,
                    'name'       => $i->product->name,
                    'quantity'   => $i->quantity,
                    'unit_price' => $i->unit_price,
                    'total'      => $i->quantity * $i->unit_price,
                ]);

                // Scala stock
                $i->product->decrement('stock', $i->quantity);
            }

            // Svuota carrello
            $cart->items()->delete();

            return $order;
        });
    }
}
