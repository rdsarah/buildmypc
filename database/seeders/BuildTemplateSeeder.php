<?php

namespace Database\Seeders;

use App\Models\BuildTemplate;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BuildTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure products are seeded first
        if (Product::count() === 0) {
            $this->call(ProductCatalogSeeder::class);
        }

        // Get category IDs
        $categories = Category::pluck('id', 'name')->toArray();

        // 1. Budget Gaming Template
        $budgetGaming = BuildTemplate::create([
            'name' => 'Budget Gaming',
            'description' => 'A starter gaming PC capable of running modern esports titles and standard 1080p games at solid framerates.',
            'target_use' => 'gaming',
        ]);

        $this->attachBestFit($budgetGaming, [
            'CPU' => ['max_price' => 18000],
            'Motherboard' => ['max_price' => 12000],
            'RAM' => ['max_price' => 6000],
            'GPU' => ['max_price' => 35000],
            'Storage' => ['max_price' => 5000],
            'PSU' => ['max_price' => 5500],
            'Casing' => ['max_price' => 5000],
        ], $categories);

        // 2. 4K Editing Powerhouse Template
        $editingPower = BuildTemplate::create([
            'name' => '4K Editing Powerhouse',
            'description' => 'Built for video editors, content creators, and heavy multitasking workloads, optimized for fast rendering and storage throughput.',
            'target_use' => 'editing',
        ]);

        $this->attachBestFit($editingPower, [
            'CPU' => ['min_price' => 25000],
            'Motherboard' => ['min_price' => 15000],
            'RAM' => ['min_price' => 7000],
            'GPU' => ['min_price' => 50000],
            'Storage' => ['min_price' => 6000],
            'PSU' => ['min_price' => 8000],
            'Casing' => ['min_price' => 6000],
        ], $categories);

        // 3. Standard Office Desktop Template
        $officeDesktop = BuildTemplate::create([
            'name' => 'Standard Office Desktop',
            'description' => 'Reliable, energy-efficient workstation for everyday productivity tasks, web browsing, spreadsheet work, and office administration.',
            'target_use' => 'office',
        ]);

        $this->attachBestFit($officeDesktop, [
            'CPU' => ['max_price' => 13000],
            'Motherboard' => ['max_price' => 9000],
            'RAM' => ['max_price' => 4000],
            'Storage' => ['max_price' => 4500],
            'PSU' => ['max_price' => 4000],
            'Casing' => ['max_price' => 3500],
        ], $categories);
    }

    private function attachBestFit(BuildTemplate $template, array $criteria, array $categories): void
    {
        foreach ($criteria as $catName => $rules) {
            if (!isset($categories[$catName])) {
                continue;
            }

            $query = Product::where('category_id', $categories[$catName])
                ->where('is_active', true);

            if (isset($rules['max_price'])) {
                $query->where('price', '<=', $rules['max_price']);
            }

            if (isset($rules['min_price'])) {
                $query->where('price', '>=', $rules['min_price']);
            }

            // Get product closest to the price limit (if max_price set, get most expensive under limit; if min_price, get cheapest above it)
            if (isset($rules['max_price'])) {
                $product = $query->orderBy('price', 'desc')->first();
            } else {
                $product = $query->orderBy('price', 'asc')->first();
            }

            // Fallback to absolute cheapest if no fit found
            if (!$product) {
                $product = Product::where('category_id', $categories[$catName])
                    ->where('is_active', true)
                    ->orderBy('price', 'asc')
                    ->first();
            }

            if ($product) {
                $template->products()->attach($product->id);
            }
        }
    }
}
