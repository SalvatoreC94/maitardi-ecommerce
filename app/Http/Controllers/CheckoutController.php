<?php

namespace App\Http\Controllers;

use App\Services\CartManager;
use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function show(Request $request, CartManager $cartManager)
    {
        $cart = $cartManager->getDetails();
        $shippingMethods = ShippingMethod::where('is_active', true)->get(['id','name','rate']);
        return view('checkout', compact('cart','shippingMethods'));
    }

    public function place(Request $request, CartManager $cartManager, OrderService $orderService)
    {
        $validated = $request->validate([
            'customer_email'     => 'required|email',
            'customer_name'      => 'required|string|max:100',
            'customer_phone'     => 'nullable|string|max:30',
            'billing.via'        => 'required|string|max:120',
            'billing.citta'      => 'required|string|max:80',
            'billing.cap'        => 'required|string|max:10',
            'shipping.via'       => 'required|string|max:120',
            'shipping.citta'     => 'required|string|max:80',
            'shipping.cap'       => 'required|string|max:10',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'notes'              => 'nullable|string|max:500',
        ]);

        $cart = $cartManager->getCart();

        try {
            $order = $orderService->createFromCart($cart, $validated, Auth::id());
        } catch (\Throwable $e) {
            return back()->withErrors(['checkout' => $e->getMessage()])->withInput();
        }

        return redirect()->route('checkout.success', ['code' => $order->code]);
    }

    public function success(string $code)
    {
        return view('checkout-success', compact('code'));
    }
}
