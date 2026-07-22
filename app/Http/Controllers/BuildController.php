<?php

namespace App\Http\Controllers;

use App\Models\Build;
use App\Models\BuildItem;
use App\Models\Category;
use App\Models\Product;
use App\Services\CompatibilityService;
use Illuminate\Http\Request;

class BuildController extends Controller
{
    private array $requiredCategories = [
        'CPU',
        'Motherboard',
        'RAM',
        'GPU',
        'Storage',
        'PSU',
        'Casing',
    ];

    public function index(Request $request, CompatibilityService $compatibilityService)
    {
        $build = $this->currentBuild($request);

        $build->load([
            'items.product.category',
            'items.product.brand',
            'items.category',
        ]);

        $categories = Category::whereIn('name', $this->requiredCategories)
            ->with([
                'products' => function ($query) {
                    $query->where('is_active', true)
                        ->where('stock', '>', 0)
                        ->with('brand')
                        ->orderBy('name');
                },
            ])
            ->get()
            ->sortBy(fn ($category) => array_search($category->name, $this->requiredCategories))
            ->values();

        $analysis = $compatibilityService->analyze($build);

        return view('builder.index', compact('build', 'categories', 'analysis'));
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $category = Category::findOrFail($validated['category_id']);

        $product = Product::where('id', $validated['product_id'])
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($product->stock <= 0) {
            return back()->withErrors([
                'product_id' => 'This product is currently out of stock.',
            ]);
        }

        $build = $this->currentBuild($request);

        BuildItem::updateOrCreate(
            [
                'build_id' => $build->id,
                'category_id' => $category->id,
            ],
            [
                'product_id' => $product->id,
            ]
        );

        return redirect()
            ->route('builder.index')
            ->with('success', "{$category->name} selected successfully.");
    }

    public function destroyItem(Request $request, BuildItem $buildItem)
    {
        $build = $this->currentBuild($request);

        abort_unless($buildItem->build_id === $build->id, 403);

        $categoryName = $buildItem->category->name ?? 'Component';

        $buildItem->delete();

        return redirect()
            ->route('builder.index')
            ->with('success', "{$categoryName} removed from build.");
    }

    public function reset(Request $request)
    {
        $build = $this->currentBuild($request);

        $build->items()->delete();

        return redirect()
            ->route('builder.index')
            ->with('success', 'Build reset successfully.');
    }

    private function currentBuild(Request $request): Build
    {
        return Build::firstOrCreate(
            [
                'session_id' => $request->session()->getId(),
                'status' => 'draft',
            ],
            [
                'name' => 'My Custom PC Build',
                'user_id' => auth()->check() ? auth()->id() : null,
                'total_price' => 0,
                'estimated_wattage' => 0,
            ]
        );
    }
}