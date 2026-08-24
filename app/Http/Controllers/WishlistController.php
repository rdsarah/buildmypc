<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display the logged-in user's wishlist.
     */
    public function index()
    {
        $wishlists = Wishlist::with(['product.category', 'product.brand'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(9);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Add a product to the wishlist.
     */
    public function store(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        Wishlist::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Product added to your wishlist.');
    }

    /**
     * Remove a product from the wishlist.
     */
    public function destroy(Product $product)
    {
        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Product removed from your wishlist.');
    }
}