<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display order history and handle tracking lookups.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $trackedOrder = null;

        if ($request->filled('order_number')) {
            $searchQuery = trim($request->input('order_number'));

            $trackedOrder = Order::where('order_number', $searchQuery)
                ->where('user_id', $userId)
                ->with('items.product')
                ->first();

            if (!$trackedOrder) {
                $trackedOrder = Order::where('order_number', $searchQuery)
                    ->with('items.product')
                    ->first();
            }

            if (!$trackedOrder) {
                session()->now('error', 'No order found matching number: ' . $searchQuery);
            }
        }

        $orders = Order::where('user_id', $userId)
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders', 'trackedOrder'));
    }

    /**
     * Cancel an active order (pending or processing).
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled at its current status.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => 'User',
            'cancellation_reason' => $request->input('reason', 'Cancelled by customer from order history page.')
        ]);

        return redirect()->back()->with('success', 'Order #' . $order->order_number . ' has been successfully cancelled.');
    }
}