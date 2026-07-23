<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $products = Product::with(['category', 'brand'])
            ->orderBy('name')
            ->paginate(10, ['*'], 'products_page')
            ->withQueryString();

        $orders = Order::with('items.product')
            ->latest()
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();

        // Statistics
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $usersCount = User::count();
        
        // Sum total of all orders
        $totalRevenue = Order::sum('total_price');

        return view('admin.dashboard', compact(
            'products',
            'orders',
            'productsCount',
            'ordersCount',
            'usersCount',
            'totalRevenue'
        ));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->back()->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        return redirect()->back()->with('success', "Order #{$order->id} status updated to '{$request->status}'.");
    }
}
