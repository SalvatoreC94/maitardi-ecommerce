<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartManager;

class CartController extends Controller
{
    protected $cart;

    public function __construct(CartManager $cartManager)
    {
        $this->cart = $cartManager;
    }

    public function index()
    {
        return response()->json($this->cart->getDetails());
    }

    public function add(Request $request, int $productId)
    {
        $quantity = $request->input('quantity', 1);
        $this->cart->addItem($productId, $quantity);
        return response()->json($this->cart->getDetails());
    }

    public function update(Request $request, int $itemId)
    {
        $quantity = $request->input('quantity', 1);
        $this->cart->updateItem($itemId, $quantity);
        return response()->json($this->cart->getDetails());
    }

    public function remove(int $itemId)
    {
        $this->cart->removeItem($itemId);
        return response()->json($this->cart->getDetails());
    }

    public function clear()
    {
        $this->cart->clear();
        return response()->json($this->cart->getDetails());
    }
}
