<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    /**
     * Display the Standalone Stripe Checkout Test Page
     */
    public function showCheckout()
    {
        return view('cart.stripe', [
            'stripeKey' => config('services.stripe.key'),
            'total' => 999.00,
        ]);
    }

    /**
     * Create Stripe Checkout Session
     */
    public function createSession(Request $request)
    {
        try {
            $secretKey = config('services.stripe.secret');

            if (!$secretKey) {
                return response()->json(['error' => 'Stripe Secret Key is missing from .env or config/services.php'], 500);
            }

            Stripe::setApiKey($secretKey);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Custom Gaming PC Build (Dummy Test)',
                        ],
                        'unit_amount' => 99900, // $999.00
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.success'),
                'cancel_url' => route('checkout.cancel'),
            ]);

            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function success()
    {
        return view('orders.index')->with('success', 'Payment successful! Your test order was placed.');
    }

    public function cancel()
    {
        return redirect()->route('checkout.stripe')->with('error', 'Payment was canceled.');
    }
}