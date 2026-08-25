<?php

namespace App\Http\Controllers;

use App\Models\BuildTemplate;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class BuildController extends Controller
{
    public function index()
    {
        $build = session()->get('active_build', []);

        $selectedProducts = Product::with(['category', 'brand'])
            ->whereIn('id', array_values($build))
            ->get()
            ->keyBy(fn($p) => $p->category->name);

        $totalPrice = $selectedProducts->sum('price');
        $totalWattage = $selectedProducts->sum('wattage');

        // FEATURE 3: BUILD COMPLETENESS VALIDATION

        $requiredComponents = [
            'CPU',
            'Motherboard',
            'RAM',
            'GPU',
            'Storage',
            'PSU',
            'Casing',
        ];

        $missingComponents = [];

        foreach ($requiredComponents as $component) {
            if (!$selectedProducts->has($component)) {
                $missingComponents[] = $component;
            }
        }

        $isBuildComplete = empty($missingComponents);

        // FEATURE 4 + 5: COMPATIBILITY CHECKING
        // AND COMPATIBLE ALTERNATIVE SUGGESTIONS

        $warnings = [];
        $alternatives = [];
        $compatibilityChecked = false;

        $cpu = null;
        $mobo = null;
        $ram = null;
        $gpu = null;
        $casing = null;
        $psu = null;

        if ($isBuildComplete) {

            $cpu = $selectedProducts->get('CPU');
            $mobo = $selectedProducts->get('Motherboard');
            $ram = $selectedProducts->get('RAM');
            $gpu = $selectedProducts->get('GPU');
            $casing = $selectedProducts->get('Casing');
            $psu = $selectedProducts->get('PSU');

            $compatibilityChecked = true;

            // 1. CPU + Motherboard Socket

            if (!$cpu->socket_type || !$mobo->socket_type) {

                $warnings[] = [
                    'type' => 'CPU / Motherboard',
                    'title' => 'Compatibility Data Missing',
                    'details' => 'CPU or motherboard socket information is missing, so socket compatibility cannot be verified.',
                ];

            } elseif (strtolower($cpu->socket_type) !== strtolower($mobo->socket_type)) {

                $warnings[] = [
                    'type' => 'CPU / Motherboard',
                    'title' => 'Socket Mismatch',
                    'details' => "The CPU uses {$cpu->socket_type}, while the motherboard uses {$mobo->socket_type}.",
                    'cpu_socket' => $cpu->socket_type,
                    'motherboard_socket' => $mobo->socket_type,
                ];

                $alternatives['CPU / Motherboard'] = Product::with(['category', 'brand'])
                    ->whereHas('category', function ($query) {
                        $query->where('name', 'Motherboard');
                    })
                    ->where('socket_type', $cpu->socket_type)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->where('id', '!=', $mobo->id)
                    ->orderBy('price', 'asc')
                    ->take(5)
                    ->get();
            }

            // 2. Motherboard + RAM

            if (!$mobo->ram_type || !$ram->ram_type) {

                $warnings[] = [
                    'type' => 'Motherboard / RAM',
                    'title' => 'Compatibility Data Missing',
                    'details' => 'Motherboard or RAM type information is missing, so RAM compatibility cannot be verified.',
                ];

            } elseif (strtolower($mobo->ram_type) !== strtolower($ram->ram_type)) {

                $warnings[] = [
                    'type' => 'Motherboard / RAM',
                    'title' => 'RAM Generation Mismatch',
                    'details' => "The motherboard supports {$mobo->ram_type}, but the selected RAM is {$ram->ram_type}.",
                    'motherboard_ram' => $mobo->ram_type,
                    'ram_type' => $ram->ram_type,
                ];

                $alternatives['Motherboard / RAM'] = Product::with(['category', 'brand'])
                    ->whereHas('category', function ($query) {
                        $query->where('name', 'RAM');
                    })
                    ->where('ram_type', $mobo->ram_type)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->where('id', '!=', $ram->id)
                    ->orderBy('price', 'asc')
                    ->take(5)
                    ->get();
            }

            // 3. PSU Wattage

            $requiredWattage = $totalWattage - $psu->wattage;
            $safePSULevel = $requiredWattage + 50;

            if ($psu->wattage < $safePSULevel) {

                $warnings[] = [
                    'type' => 'PSU / Build',
                    'title' => 'Insufficient PSU Wattage',
                    'details' => "The build requires approximately {$requiredWattage}W. A safer recommended minimum is {$safePSULevel}W.",
                    'required_wattage' => $requiredWattage,
                    'psu_wattage' => $psu->wattage,
                ];

                $alternatives['PSU / Build'] = Product::with(['category', 'brand'])
                    ->whereHas('category', function ($query) {
                        $query->where('name', 'PSU');
                    })
                    ->where('wattage', '>=', $safePSULevel)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->where('id', '!=', $psu->id)
                    ->orderBy('price', 'asc')
                    ->take(5)
                    ->get();
            }

            // 4. GPU + Casing Clearance

            if (!$gpu->gpu_length_mm || !$casing->case_clearance_mm) {

                $warnings[] = [
                    'type' => 'GPU / Casing',
                    'title' => 'Compatibility Data Missing',
                    'details' => 'GPU length or case clearance information is missing, so physical fit cannot be verified.',
                ];

            } elseif ($gpu->gpu_length_mm > $casing->case_clearance_mm) {

                $warnings[] = [
                    'type' => 'GPU / Casing',
                    'title' => 'GPU Too Long',
                    'details' => "The GPU is {$gpu->gpu_length_mm}mm long, but the casing supports GPUs up to {$casing->case_clearance_mm}mm.",
                    'gpu_length' => $gpu->gpu_length_mm,
                    'case_clearance' => $casing->case_clearance_mm,
                ];

                $alternatives['GPU / Casing'] = Product::with(['category', 'brand'])
                    ->whereHas('category', function ($query) {
                        $query->where('name', 'Casing');
                    })
                    ->where('case_clearance_mm', '>=', $gpu->gpu_length_mm)
                    ->where('is_active', true)
                    ->where('stock', '>', 0)
                    ->where('id', '!=', $casing->id)
                    ->orderBy('price', 'asc')
                    ->take(5)
                    ->get();
            }
        }

        // BUILD TEMPLATES

        $templates = BuildTemplate::with('products.category')->get();

        return view('builder.index', compact(
            'selectedProducts',
            'totalPrice',
            'totalWattage',
            'warnings',
            'templates',
            'missingComponents',
            'isBuildComplete',
            'cpu',
            'mobo',
            'ram',
            'gpu',
            'casing',
            'psu',
            'alternatives',
            'compatibilityChecked',
        ));
    }

    public function addComponent(Request $request, Product $product)
    {
        $build = session()->get('active_build', []);
        $build[$product->category->name] = $product->id;
        session()->put('active_build', $build);

        return redirect()->route('builder.index')->with('success', "'{$product->name}' added to your build.");
    }

    public function removeComponent($categoryName)
    {
        $build = session()->get('active_build', []);
        if (isset($build[$categoryName])) {
            unset($build[$categoryName]);
            session()->put('active_build', $build);
        }

        return redirect()->route('builder.index')->with('success', "Removed {$categoryName} from your build.");
    }

    public function clearBuild()
    {
        session()->forget('active_build');
        return redirect()->route('builder.index')->with('success', "Current build cleared.");
    }

    public function cloneTemplate(BuildTemplate $template)
    {
        $build = [];
        foreach ($template->products as $product) {
            $build[$product->category->name] = $product->id;
        }
        session()->put('active_build', $build);

        return redirect()->route('builder.index')->with('success', "Cloned template '{$template->name}' as your active build.");
    }

    public function suggestBuild(Request $request)
    {
        $request->validate([
            'budget' => 'required|numeric|min:20000',
            'focus' => 'required|string|in:gaming,editing,office',
        ]);

        $budget = $request->budget;
        $focus = $request->focus;

        // Allocation weight systems
        $allocations = [
            'gaming' => [
                'CPU' => 0.22,
                'Motherboard' => 0.13,
                'RAM' => 0.08,
                'GPU' => 0.35,
                'Storage' => 0.07,
                'PSU' => 0.08,
                'Casing' => 0.07,
            ],
            'editing' => [
                'CPU' => 0.30,
                'Motherboard' => 0.15,
                'RAM' => 0.15,
                'GPU' => 0.18,
                'Storage' => 0.10,
                'PSU' => 0.06,
                'Casing' => 0.06,
            ],
            'office' => [
                'CPU' => 0.35,
                'Motherboard' => 0.20,
                'RAM' => 0.15,
                'Storage' => 0.15,
                'PSU' => 0.08,
                'Casing' => 0.07,
            ]
        ];

        $weights = $allocations[$focus];
        $build = [];

        foreach ($weights as $catName => $weight) {
            $category = Category::where('name', $catName)->first();
            if (!$category) {
                continue;
            }

            $targetPrice = $budget * $weight;

            // Search for products under target price
            $product = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->where('price', '<=', $targetPrice)
                ->orderBy('price', 'desc')
                ->first();

            // Fallback to cheapest if none fits under the allocation
            if (!$product) {
                $product = Product::where('category_id', $category->id)
                    ->where('is_active', true)
                    ->orderBy('price', 'asc')
                    ->first();
            }

            if ($product) {
                $build[$catName] = $product->id;
            }
        }

        session()->put('active_build', $build);

        return redirect()->route('builder.index')->with('success', "Auto-populated a {$focus}-focused build under ৳" . number_format($budget) . ".");
    }

    public function gallery()
    {
        $currentUserId = auth()->id();

        // Safe query structure that handles guest & authenticated conditions without scoping issues
        $builds = BuildTemplate::with(['user', 'products.category'])
            ->where(function ($query) use ($currentUserId) {
                $query->whereNull('user_id')
                      ->orWhereHas('user', function ($q) {
                          $q->where('role', 'admin');
                      });

                if ($currentUserId) {
                    $query->orWhere('user_id', $currentUserId);
                }
            })
            ->latest()
            ->paginate(12);

        return view('builder.gallery', compact('builds'));
    }

    public function store(Request $request)
    {
        // 1. Validate incoming build data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        // 2. Create the saved build record
        $build = BuildTemplate::create([
            'user_id' => auth()->id() ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'target_use' => 'Custom',
        ]);

        // 3. Attach selected component product IDs into the pivot table
        $build->products()->attach($validated['product_ids']);

        return redirect()->route('gallery.index')->with('success', 'Build saved successfully!');
    }

    public function destroy($id)
    {
        $build = BuildTemplate::findOrFail($id);
        
        // Detach pivot relationship first, then delete build
        $build->products()->detach();
        $build->delete();

        return redirect()->route('gallery.index')->with('success', 'Build deleted successfully!');
    }

    /**
     * Edit a saved build (loads its products into the active builder session).
     */
    public function edit($id)
    {
        $build = BuildTemplate::with('products.category')->findOrFail($id);

        // Standardized session key to 'active_build' so builder recognizes it
        $selected = [];
        foreach ($build->products as $product) {
            if ($product->category) {
                $selected[$product->category->name] = $product->id;
            }
        }

        session(['active_build' => $selected]);

        return redirect()->route('builder.index')->with('success', "Loaded '{$build->name}' into builder for editing!");
    }

    /**
     * Add all build components to the shopping cart.
     */
    public function addToCart($id)
    {
        $build = BuildTemplate::with('products')->findOrFail($id);
        $cart = session()->get('cart', []);

        foreach ($build->products as $product) {
            if (isset($cart[$product->id])) {
                $cart[$product->id]['quantity']++;
            } else {
                $cart[$product->id] = [
                    'name'     => $product->name,
                    'quantity' => 1,
                    'price'    => $product->price,
                    'image'    => $product->image ?? null,
                ];
            }
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', "All components from '{$build->name}' were added to your cart!");
    }
}