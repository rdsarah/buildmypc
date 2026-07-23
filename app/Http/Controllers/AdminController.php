<?php

namespace App\Http\Controllers;

use App\Models\BuildTemplate;
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

        $templates = BuildTemplate::with('products.category')->latest()->get();

        // Group products by category for template creation dropdowns
        $productsByCategory = Product::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy(fn($p) => $p->category->name);

        // Statistics
        $productsCount = Product::count();
        $ordersCount = Order::count();
        $usersCount = User::count();
        
        // Sum total of all orders
        $totalRevenue = Order::sum('total_price');

        return view('admin.dashboard', compact(
            'products',
            'orders',
            'templates',
            'productsByCategory',
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

    public function createTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_use' => 'required|string|in:gaming,editing,office',
            'products' => 'required|array',
            'products.*' => 'nullable|exists:products,id',
        ]);

        $template = BuildTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'target_use' => $request->target_use,
        ]);

        $productIds = array_filter($request->products);
        $template->products()->attach($productIds);

        return redirect()->back()->with('success', "Starter template '{$template->name}' created successfully.");
    }

    public function deleteTemplate(BuildTemplate $template)
    {
        $template->delete();

        return redirect()->back()->with('success', "Template '{$template->name}' deleted successfully.");
    }
}
