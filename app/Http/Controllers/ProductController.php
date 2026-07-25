<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Review;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $productsQuery = Product::query()
            ->with(['category', 'brand'])
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;

            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($search) {
                        $brandQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category')) {
            $productsQuery->whereHas('category', function ($query) use ($request) {
                $query->where('slug', $request->category);
            });
        }

        if ($request->filled('brand')) {
            $productsQuery->whereHas('brand', function ($query) use ($request) {
                $query->where('slug', $request->brand);
            });
        }

        if ($request->filled('socket_type')) {
            $productsQuery->where('socket_type', $request->socket_type);
        }

        if ($request->filled('ram_type')) {
            $productsQuery->where('ram_type', $request->ram_type);
        }

        if ($request->filled('form_factor')) {
            $productsQuery->where('form_factor', $request->form_factor);
        }

        if ($request->filled('storage_type')) {
            $productsQuery->where('storage_type', $request->storage_type);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $productsQuery->where('stock', '>', 0);
            }

            if ($request->stock_status === 'out_of_stock') {
                $productsQuery->where('stock', '<=', 0);
            }
        }

        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', $request->max_price);
        }

        match ($request->sort) {
            'price_low' => $productsQuery->orderBy('price', 'asc'),
            'price_high' => $productsQuery->orderBy('price', 'desc'),
            'brand' => $productsQuery
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->orderBy('brands.name', 'asc')
                ->select('products.*'),
            'stock' => $productsQuery->orderBy('stock', 'desc'),
            default => $productsQuery->latest(),
        };

        $products = $productsQuery->paginate(9)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        $socketTypes = Product::whereNotNull('socket_type')
            ->where('socket_type', '!=', '')
            ->distinct()
            ->orderBy('socket_type')
            ->pluck('socket_type');

        $ramTypes = Product::whereNotNull('ram_type')
            ->where('ram_type', '!=', '')
            ->distinct()
            ->orderBy('ram_type')
            ->pluck('ram_type');

        $formFactors = Product::whereNotNull('form_factor')
            ->where('form_factor', '!=', '')
            ->distinct()
            ->orderBy('form_factor')
            ->pluck('form_factor');

        $storageTypes = Product::whereNotNull('storage_type')
            ->where('storage_type', '!=', '')
            ->distinct()
            ->orderBy('storage_type')
            ->pluck('storage_type');

        return view('products.index', compact(
            'products',
            'categories',
            'brands',
            'socketTypes',
            'ramTypes',
            'formFactors',
            'storageTypes'
        ));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'brand', 'reviews.user']);

        $relatedProducts = Product::with(['category', 'brand'])
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(3)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    

// In your method that shows product details:

        // review method
    public function storeReview(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'product_id' => $productId,
            'user_id'    => auth()->id(), // null if guest
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    // delete review
    public function destroyReview(Review $review)
        {
            // Ensure only the owner of the review (or an admin) can delete it
            if (auth()->id() !== $review->user_id) {
                abort(403, 'Unauthorized action.');
            }

            $review->delete();

            return back()->with('success', 'Review deleted successfully!');
        }
}