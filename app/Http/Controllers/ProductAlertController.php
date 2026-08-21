<?php

namespace App\Http\Controllers;

use App\Models\ProductAlert;

class ProductAlertController extends Controller
{
    /**
     * Display the logged-in user's product alerts.
     */
    public function index()
    {
        $alerts = ProductAlert::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->paginate(10);

        return view('product-alerts.index', compact('alerts'));
    }

    /**
     * Mark an alert as read.
     */
    public function markAsRead(ProductAlert $alert)
    {
        // Make sure users can only modify their own alerts.
        if ($alert->user_id !== auth()->id()) {
            abort(403);
        }

        $alert->update([
            'is_read' => true,
        ]);

        return back();
    }

    /**
     * Mark all alerts as read.
     */
    public function markAllAsRead()
    {
        ProductAlert::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
            ]);

        return back();
    }
}