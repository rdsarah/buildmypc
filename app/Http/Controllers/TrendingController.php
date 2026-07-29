<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ComparisonHistory;
use App\Models\Product;
use Illuminate\Http\Request;

class TrendingController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategoryId = $request->category;

        $histories = ComparisonHistory::all();

        $productCounts = [];

        foreach ($histories as $history) {
            foreach ($history->product_ids as $productId) {
                if (isset($productCounts[$productId])) {
                    $productCounts[$productId]++;
                } else {
                    $productCounts[$productId] = 1;
                }
            }
        }

        arsort($productCounts);

        $products = Product::with(['category', 'brand'])
            ->whereIn('id', array_keys($productCounts))
            ->get()
            ->keyBy('id');

        $trendingProducts = collect();

        foreach ($productCounts as $productId => $count) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            if ($selectedCategoryId && $product->category_id != $selectedCategoryId) {
                continue;
            }

            $product->trend_count = $count;
            $trendingProducts->push($product);
        }

        $trendingProducts = $trendingProducts
            ->sortByDesc('trend_count')
            ->take(10)
            ->values();

        $globalTopProduct = null;

        if (!empty($productCounts)) {
            $globalTopProductId = array_key_first($productCounts);
            $globalTopProduct = $products[$globalTopProductId] ?? null;
        }

        $categoryCounts = [];
        $brandCounts = [];

        foreach ($productCounts as $productId => $count) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];

            if ($product->category) {
                $categoryName = $product->category->name;

                if (isset($categoryCounts[$categoryName])) {
                    $categoryCounts[$categoryName] += $count;
                } else {
                    $categoryCounts[$categoryName] = $count;
                }
            }

            if ($product->brand) {
                $brandName = $product->brand->name;

                if (isset($brandCounts[$brandName])) {
                    $brandCounts[$brandName] += $count;
                } else {
                    $brandCounts[$brandName] = $count;
                }
            }
        }

        arsort($categoryCounts);
        arsort($brandCounts);

        $topCategory = array_key_first($categoryCounts);
        $topBrand = array_key_first($brandCounts);

        $categories = Category::orderBy('name')->get();

        return view('trending.index', compact(
            'histories',
            'trendingProducts',
            'globalTopProduct',
            'topCategory',
            'topBrand',
            'categories',
            'selectedCategoryId'
        ));
    }
}