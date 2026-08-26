<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\ProductAlert;
use App\Models\ComparisonHistory;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    /**
     * Display the personal analytics dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        $wishlistCount = Wishlist::where('user_id', $user->id)->count();

        $alertCount = ProductAlert::where('user_id', $user->id)->count();

        $unreadAlertCount = ProductAlert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $comparisonCount = ComparisonHistory::where('user_id', $user->id)->count();

        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product.category')
            ->get();

        $wishlistValue = $wishlistItems->sum(function ($wishlist) {
            return $wishlist->product->price ?? 0;
        });

        $wishlistByCategory = $wishlistItems
            ->groupBy(function ($wishlist) {
                return $wishlist->product->category->name ?? 'Other';
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc();

        $priceDropAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'price_drop')
            ->count();

        $backInStockAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'back_in_stock')
            ->count();

        $combinedAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'back_in_stock_and_price_drop')
            ->count();

        $comparisonByCategory = ComparisonHistory::where('user_id', $user->id)
            ->with('category')
            ->get()
            ->groupBy(function ($comparison) {
                return $comparison->category->name ?? 'Other';
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc();

        $mostComparedCategory = $comparisonByCategory->keys()->first();

        return view('analytics.index', compact(
            'wishlistCount',
            'alertCount',
            'unreadAlertCount',
            'comparisonCount',
            'wishlistValue',
            'wishlistByCategory',
            'priceDropAlerts',
            'backInStockAlerts',
            'combinedAlerts',
            'comparisonByCategory',
            'mostComparedCategory'
        ));
    }

    /**
     * Download personal analytics as PDF.
     */
    public function downloadPdf()
    {
        $user = Auth::user();

        $wishlistItems = Wishlist::where('user_id', $user->id)
            ->with('product.category')
            ->get();

        $wishlistCount = $wishlistItems->count();

        $wishlistValue = $wishlistItems->sum(function ($wishlist) {
            return $wishlist->product->price ?? 0;
        });

        $wishlistByCategory = $wishlistItems
            ->groupBy(function ($wishlist) {
                return $wishlist->product->category->name ?? 'Other';
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc();

        $alertCount = ProductAlert::where('user_id', $user->id)->count();

        $unreadAlertCount = ProductAlert::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $priceDropAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'price_drop')
            ->count();

        $backInStockAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'back_in_stock')
            ->count();

        $combinedAlerts = ProductAlert::where('user_id', $user->id)
            ->where('type', 'back_in_stock_and_price_drop')
            ->count();

        $comparisonCount = ComparisonHistory::where('user_id', $user->id)->count();

        $comparisonByCategory = ComparisonHistory::where('user_id', $user->id)
            ->with('category')
            ->get()
            ->groupBy(function ($comparison) {
                return $comparison->category->name ?? 'Other';
            })
            ->map(function ($items) {
                return $items->count();
            })
            ->sortDesc();

        $mostComparedCategory = $comparisonByCategory->keys()->first();

        $pdf = Pdf::loadView('analytics.pdf', compact(
            'user',
            'wishlistCount',
            'wishlistValue',
            'wishlistByCategory',
            'alertCount',
            'unreadAlertCount',
            'priceDropAlerts',
            'backInStockAlerts',
            'combinedAlerts',
            'comparisonCount',
            'comparisonByCategory',
            'mostComparedCategory'
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('buildmypc-analytics.pdf');
    }
}
