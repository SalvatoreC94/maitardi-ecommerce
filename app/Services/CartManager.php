<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CartManager
{
    protected $cart;

    public function __construct()
    {
        $this->cart = $this->getCart();
    }

    public function getCart(): Cart
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['id' => (string) Str::uuid()]
            );

            // Merge guest -> user
            if (session()->has('cart_session_id')) {
                $guestId = session('cart_session_id');
                $guestCart = Cart::where('session_id', $guestId)->first();
                if ($guestCart) {
                    foreach ($guestCart->items as $item) {
                        $this->addItem($item->product_id, $item->quantity, $item->unit_price);
                    }
                    $guestCart->delete();
                }
                session()->forget('cart_session_id');
            }

            return $cart;
        }

        // Guest
        $sessionId = session('cart_session_id', (string) Str::uuid());
        session(['cart_session_id' => $sessionId]);

        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['id' => (string) Str::uuid()]
        );
    }

    public function addItem(int $productId, int $quantity = 1, ?float $price = null): CartItem
    {
        $product = Product::findOrFail($productId);
        $item = $this->cart->items()->where('product_id', $productId)->first();
        $unitPrice = $price ?? $product->price;

        if ($item) {
            $item->quantity += $quantity;
            $item->unit_price = $unitPrice;
            $item->save();
        } else {
            $item = $this->cart->items()->create([
                'product_id' => $productId,
                'quantity'   => $quantity,
                'unit_price' => $unitPrice,
            ]);
        }

        return $item;
    }

    public function updateItem(int $itemId, int $quantity): bool
    {
        $item = $this->cart->items()->findOrFail($itemId);
        if ($quantity <= 0) {
            return (bool) $item->delete();
        }
        $item->quantity = $quantity;
        return $item->save();
    }

    public function removeItem(int $itemId): bool
    {
        return (bool) $this->cart->items()->findOrFail($itemId)->delete();
    }

    public function clear(): void
    {
        $this->cart->items()->delete();
    }

    public function calculateTotals(): array
    {
        $subtotal = $this->cart->items->sum(fn($item) => $item->quantity * $item->unit_price);
        $shipping = 6.90; // base
        $total = $subtotal + $shipping;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total'    => $total,
        ];
    }

    public function getDetails(): array
    {
        return [
            'id'    => $this->cart->id,
            'items' => $this->cart->items()->with('product')->get()->toArray(),
            'totals'=> $this->calculateTotals(),
        ];
    }
}
