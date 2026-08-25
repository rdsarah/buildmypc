<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * Display Stripe checkout page.
     */
    public function showCheckout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = 0;
        foreach ($cart as $item) {
            $price = $item['price'] ?? 0;
            $qty   = $item['quantity'] ?? 1;
            $total += $price * $qty;
        }

        return view('checkout.stripe', [
            'stripeKey' => config('services.stripe.key'),
            'cart'      => $cart,
            'total'     => $total,
        ]);
    }

    /**
     * Create Stripe Checkout Session dynamically.
     */
    public function createSession(Request $request)
    {
        try {
            $secretKey = config('services.stripe.secret');

            if (!$secretKey) {
                return response()->json(['error' => 'Stripe Secret Key is missing.'], 500);
            }

            // Bypass Stripe for dummy keys to allow testing the checkout flow
            if ($secretKey === 'sk_test_dummy_key_12345') {
                return response()->json(['redirect' => route('checkout.success')]);
            }

            $cart = session()->get('cart', []);

            if (empty($cart)) {
                return response()->json(['error' => 'Cart is empty.'], 400);
            }

            Stripe::setApiKey($secretKey);

            $lineItems = [];
            $bdtToUsdRate = 120; // 1 USD = 120 BDT

            foreach ($cart as $item) {
                $name     = $item['name'] ?? 'PC Component';
                $priceBdt = (float) ($item['price'] ?? 0);
                $qty      = (int) ($item['quantity'] ?? 1);

                // Convert BDT price to USD
                $priceUsd = $priceBdt / $bdtToUsdRate;

                $lineItems[] = [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => $name . ' (৳' . number_format($priceBdt, 0) . ')',
                        ],
                        'unit_amount'  => (int) round($priceUsd * 100), // convert to USD cents
                    ],
                    'quantity'   => $qty,
                ];
            }

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'success_url'          => route('checkout.success'),
                'cancel_url'           => route('checkout.cancel'),
            ]);

            return response()->json(['id' => $session->id]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Handle payment success.
     */
    public function success()
    {
        $cart = session()->get('cart', []);

        if (!empty($cart)) {
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
            }

            $order = Order::create([
                'user_id'      => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => $totalAmount,
                'status'       => 'processing',
            ]);

            foreach ($cart as $id => $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $id,
                    'quantity'   => $item['quantity'] ?? 1,
                    'price'      => $item['price'] ?? 0,
                ]);
            }

            session()->forget('cart');
        }

        return redirect()->route('orders.index')->with('success', 'Payment successful! Order placed.');
    }

    /**
     * Handle payment cancellation.
     */
    public function cancel()
    {
        return redirect()->route('cart.index')->with('error', 'Payment was canceled.');
    }
}