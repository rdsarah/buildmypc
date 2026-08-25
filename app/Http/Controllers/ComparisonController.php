<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ComparisonHistory;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class ComparisonController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $products = collect();
        $selectedProducts = collect();
        $recommendedProduct = null;
        $recommendationText = null;

        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();

            if ($category) {
                $query = Product::with(['brand', 'category'])
                    ->where('category_id', $category->id)
                    ->where('is_active', true);

                if (Setting::get('hide_out_of_stock', '0') === '1') {
                    $query->where('stock', '>', 0);
                }

                $products = $query->orderBy('name')->get();
            }
        }

        if ($request->products) {
            $request->validate([
                'category' => 'required',
                'products' => 'required|array|min:2|max:3',
                'products.*' => 'exists:products,id',
                'priority' => 'required|in:balanced,lowest_price,availability,power_efficiency',
            ]);

            $category = Category::where('slug', $request->category)->first();

            if (!$category) {
                return back()->withErrors([
                    'category' => 'Please select a valid category.',
                ]);
            }

            $selectedProducts = Product::with(['brand', 'category'])
                ->whereIn('id', $request->products)
                ->where('category_id', $category->id)
                ->where('is_active', true)
                ->get();

            if ($selectedProducts->count() < 2) {
                return back()->withInput()->withErrors([
                    'products' => 'Please select at least two products from the same category.',
                ]);
            }

            if ($request->priority === 'lowest_price') {
                $recommendedProduct = $selectedProducts
                    ->sortBy('price')
                    ->first();

                $recommendationText =
                    'This product has the lowest price among the selected products.';
            } elseif ($request->priority === 'availability') {
                $recommendedProduct = $selectedProducts
                    ->sortByDesc('stock')
                    ->first();

                $recommendationText =
                    'This product currently has the highest stock.';
            } elseif ($request->priority === 'power_efficiency') {
                $productsWithWattage = $selectedProducts
                    ->whereNotNull('wattage')
                    ->where('wattage', '>', 0);

                if ($productsWithWattage->count() > 0) {
                    $recommendedProduct = $productsWithWattage
                        ->sortBy('wattage')
                        ->first();

                    $recommendationText =
                        'This product has the lowest listed wattage.';
                } else {
                    $recommendedProduct = $selectedProducts
                        ->sortBy('price')
                        ->first();

                    $recommendationText =
                        'Wattage information is unavailable, so the lowest-priced product was selected.';
                }
            } else {
                $recommendedProduct = $selectedProducts
                    ->sortBy(function ($product) {
                        $stockValue = $product->stock > 0 ? 0 : 1;

                        return [
                            $stockValue,
                            $product->price,
                        ];
                    })
                    ->first();

                $recommendationText =
                    'This product gives a simple balance between availability and price.';
            }
        }

        $selectedCategory = null;

        if ($request->category) {
            $selectedCategory = Category::where('slug', $request->category)->first();
        }

        return view('comparison.index', compact(
            'categories',
            'products',
            'selectedProducts',
            'recommendedProduct',
            'recommendationText',
            'selectedCategory'
        ));
    }
public function save(Request $request)
{
    $validated = $request->validate([
        'category_id' => 'required|exists:categories,id',
        'priority' => 'required|string',
        'products' => 'required|array|min:2|max:3',
        'products.*' => 'exists:products,id',
    ]);

    ComparisonHistory::create([
        'user_id' => Auth::id(),
        'category_id' => $validated['category_id'],
        'priority' => $validated['priority'],
        'product_ids' => $validated['products'],
    ]);

    return redirect()
        ->route('comparison.history')
        ->with('success', 'Comparison saved successfully.');
}

public function history()
{
    $comparisons = ComparisonHistory::where('user_id', Auth::id())
        ->with('category')
        ->latest()
        ->get();

    return view('comparison.history', compact('comparisons'));
}

public function destroy(ComparisonHistory $comparisonHistory)
{
    if ($comparisonHistory->user_id != Auth::id()) {
        abort(403);
    }

    $comparisonHistory->delete();

    return back()->with('success', 'Comparison deleted successfully.');
}    
}