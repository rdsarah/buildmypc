<?php

namespace App\Http\Controllers;

use App\Models\BuildTemplate;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\ProductAlert;

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

        // Store old values before updating
        $oldPrice = $product->price;
        $oldStock = $product->stock;

        // Update product
        $product->update($validated);

        // Detect relevant changes
        $priceDropped = $product->price < $oldPrice;
        $cameBackInStock = $oldStock <= 0 && $product->stock > 0;

        // Create one alert if either event happened
        if ($priceDropped || $cameBackInStock) {

            $wishlists = Wishlist::where('product_id', $product->id)->get();

            foreach ($wishlists as $wishlist) {

                // Both stock and price changed
                if ($priceDropped && $cameBackInStock) {

                    $message = "{$product->name} is back in stock and its price has dropped to ৳"
                        . number_format($product->price, 2) . ".";

                    $type = 'back_in_stock_and_price_drop';

                // Only price dropped
                } elseif ($priceDropped) {

                    $message = "{$product->name} price has dropped to ৳"
                        . number_format($product->price, 2) . ".";

                    $type = 'price_drop';

                // Only came back in stock
                } else {

                    $message = "{$product->name} is back in stock.";

                    $type = 'back_in_stock';
                }

                ProductAlert::create([
                    'user_id' => $wishlist->user_id,
                    'product_id' => $product->id,
                    'type' => $type,
                    'message' => $message,
                    'is_read' => false,
                ]);
            }
        }

        return redirect()->back()->with(
            'success',
            "Product '{$product->name}' updated successfully."
        );
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update($validated);

        \Illuminate\Support\Facades\Notification::route('mail', $order->customer_email)
            ->notify(new \App\Notifications\OrderStatusChanged($order));

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
