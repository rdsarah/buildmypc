<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart page with all added items from the database.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', auth()->id())
            ->with('product')
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += ($item->product->price ?? 0) * $item->quantity;
        }

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    /**
     * Add a product to the database cart.
     */
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = (int) $request->input('quantity', 1);

        $cartItem = Cart::firstOrNew([
            'user_id'    => auth()->id(),
            'product_id' => $product->id,
        ]);

        $cartItem->quantity = ($cartItem->exists ? $cartItem->quantity : 0) + $quantity;
        $cartItem->save();

        return redirect()->route('cart.index')->with('success', "{$product->name} added to cart!");
    }

    /**
     * Update item quantity in the database cart.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => (int) $request->quantity,
            ]);

            return back()->with('success', 'Cart updated successfully.');
        }

        return back()->with('error', 'Item not found in cart.');
    }

    /**
     * Remove an item from the database cart.
     */
    public function remove($id)
    {
        $deleted = Cart::where('user_id', auth()->id())
            ->where('id', $id)
            ->delete();

        if ($deleted) {
            return back()->with('success', 'Item removed from cart.');
        }

        return back()->with('error', 'Item not found in cart.');
    }

    /**
     * Clear all items from the user's database cart.
     */
    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();
        
        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}