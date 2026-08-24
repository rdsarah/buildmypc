<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function cancel(Order $order)
    {
        if (in_array($order->status, ['pending', 'confirmed'])) {
            $order->update(['status' => 'cancelled']);

            // Send notification of status change to customer
            \Illuminate\Support\Facades\Notification::route('mail', $order->customer_email)
                ->notify(new \App\Notifications\OrderStatusChanged($order));

            return redirect()->back()->with('success', "Order #{$order->id} has been cancelled successfully.");
        }

        return redirect()->back()->with('error', "This order cannot be cancelled.");
    }
}
