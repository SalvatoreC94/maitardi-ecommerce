<?php

namespace App\Http\Controllers;
use App\Services\CartManager;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = app(\App\Services\CartManager::class)->getDetails();
        return response()->json($cart);
    }

    public function add(Request $request, int $productId)
    {
        $quantity = $request->input('quantity', 1);
        app(\App\Services\CartManager::class)->addItem($productId, $quantity);
        return response()->json(app(\App\Services\CartManager::class)->getDetails());
    }

    public function update(Request $request, int $itemId)
    {
        $quantity = $request->input('quantity', 1);
        app(\App\Services\CartManager::class)->updateItem($itemId, $quantity);
        return response()->json(app(\App\Services\CartManager::class)->getDetails());
    }

    public function remove(int $itemId)
    {
        app(\App\Services\CartManager::class)->removeItem($itemId);
        return response()->json(app(\App\Services\CartManager::class)->getDetails());
    }

    public function clear()
    {
        app(\App\Services\CartManager::class)->clear();
        return response()->json(app(\App\Services\CartManager::class)->getDetails());
    }
}
