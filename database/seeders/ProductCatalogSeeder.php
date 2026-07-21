<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'CPU',
            'Motherboard',
            'RAM',
            'GPU',
            'Storage',
            'PSU',
            'Casing',
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName]
            );
        }

        $brands = [
            'AMD',
            'Intel',
            'MSI',
            'Gigabyte',
            'Corsair',
            'Kingston',
            'NVIDIA',
            'Samsung',
            'Seagate',
            'Cooler Master',
            'DeepCool',
            'NZXT',
        ];

        foreach ($brands as $brandName) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );
        }

        $products = [
            [
                'name' => 'AMD Ryzen 5 5600',
                'brand' => 'AMD',
                'category' => 'CPU',
                'model' => 'Ryzen 5 5600',
                'price' => 13500,
                'stock' => 12,
                'description' => 'A budget-friendly 6-core processor suitable for gaming and productivity builds.',
                'socket_type' => 'AM4',
                'wattage' => 65,
            ],
            [
                'name' => 'Intel Core i5-12400F',
                'brand' => 'Intel',
                'category' => 'CPU',
                'model' => 'i5-12400F',
                'price' => 17500,
                'stock' => 8,
                'description' => 'A strong mid-range Intel processor for gaming and everyday performance.',
                'socket_type' => 'LGA1700',
                'wattage' => 65,
            ],
            [
                'name' => 'AMD Ryzen 7 7700',
                'brand' => 'AMD',
                'category' => 'CPU',
                'model' => 'Ryzen 7 7700',
                'price' => 31500,
                'stock' => 5,
                'description' => 'A powerful AM5 processor for gaming, editing, and multitasking.',
                'socket_type' => 'AM5',
                'wattage' => 65,
            ],
            [
                'name' => 'MSI B550M PRO-VDH WiFi',
                'brand' => 'MSI',
                'category' => 'Motherboard',
                'model' => 'B550M PRO-VDH',
                'price' => 14500,
                'stock' => 6,
                'description' => 'A Micro-ATX AM4 motherboard with DDR4 RAM support.',
                'socket_type' => 'AM4',
                'ram_type' => 'DDR4',
                'form_factor' => 'Micro-ATX',
            ],
            [
                'name' => 'Gigabyte B760M DS3H DDR4',
                'brand' => 'Gigabyte',
                'category' => 'Motherboard',
                'model' => 'B760M DS3H DDR4',
                'price' => 16000,
                'stock' => 5,
                'description' => 'A motherboard for Intel LGA1700 processors with DDR4 memory support.',
                'socket_type' => 'LGA1700',
                'ram_type' => 'DDR4',
                'form_factor' => 'Micro-ATX',
            ],
            [
                'name' => 'MSI PRO B650M-A WiFi',
                'brand' => 'MSI',
                'category' => 'Motherboard',
                'model' => 'PRO B650M-A WiFi',
                'price' => 24500,
                'stock' => 4,
                'description' => 'An AM5 motherboard for modern Ryzen builds with DDR5 memory support.',
                'socket_type' => 'AM5',
                'ram_type' => 'DDR5',
                'form_factor' => 'Micro-ATX',
            ],
            [
                'name' => 'Corsair Vengeance 16GB DDR4',
                'brand' => 'Corsair',
                'category' => 'RAM',
                'model' => 'Vengeance LPX',
                'price' => 5200,
                'stock' => 20,
                'description' => '16GB DDR4 memory kit suitable for gaming and general PC builds.',
                'ram_type' => 'DDR4',
            ],
            [
                'name' => 'Kingston Fury Beast 16GB DDR5',
                'brand' => 'Kingston',
                'category' => 'RAM',
                'model' => 'Fury Beast',
                'price' => 7800,
                'stock' => 10,
                'description' => '16GB DDR5 memory for newer generation systems.',
                'ram_type' => 'DDR5',
            ],
            [
                'name' => 'NVIDIA RTX 3060 12GB',
                'brand' => 'NVIDIA',
                'category' => 'GPU',
                'model' => 'RTX 3060',
                'price' => 36000,
                'stock' => 4,
                'description' => 'A popular graphics card for 1080p and 1440p gaming.',
                'wattage' => 170,
                'gpu_length_mm' => 242,
            ],
            [
                'name' => 'AMD Radeon RX 6600',
                'brand' => 'AMD',
                'category' => 'GPU',
                'model' => 'RX 6600',
                'price' => 28500,
                'stock' => 7,
                'description' => 'A power-efficient graphics card for budget gaming builds.',
                'wattage' => 132,
                'gpu_length_mm' => 241,
            ],
            [
                'name' => 'Samsung 970 EVO Plus 500GB',
                'brand' => 'Samsung',
                'category' => 'Storage',
                'model' => '970 EVO Plus',
                'price' => 6200,
                'stock' => 15,
                'description' => 'Fast NVMe SSD storage for operating system and applications.',
                'storage_type' => 'NVMe',
            ],
            [
                'name' => 'Seagate Barracuda 1TB HDD',
                'brand' => 'Seagate',
                'category' => 'Storage',
                'model' => 'Barracuda',
                'price' => 4200,
                'stock' => 14,
                'description' => 'Large capacity hard drive for general file storage.',
                'storage_type' => 'HDD',
            ],
            [
                'name' => 'Cooler Master MWE 550W',
                'brand' => 'Cooler Master',
                'category' => 'PSU',
                'model' => 'MWE 550',
                'price' => 6200,
                'stock' => 9,
                'description' => '550W power supply suitable for entry-level and mid-range builds.',
                'wattage' => 550,
            ],
            [
                'name' => 'Corsair CV650 650W',
                'brand' => 'Corsair',
                'category' => 'PSU',
                'model' => 'CV650',
                'price' => 7600,
                'stock' => 8,
                'description' => '650W power supply for builds with stronger graphics cards.',
                'wattage' => 650,
            ],
            [
                'name' => 'DeepCool Matrexx 40',
                'brand' => 'DeepCool',
                'category' => 'Casing',
                'model' => 'Matrexx 40',
                'price' => 4800,
                'stock' => 6,
                'description' => 'Compact case with Micro-ATX support and good airflow.',
                'form_factor' => 'Micro-ATX',
                'case_clearance_mm' => 320,
            ],
            [
                'name' => 'NZXT H5 Flow',
                'brand' => 'NZXT',
                'category' => 'Casing',
                'model' => 'H5 Flow',
                'price' => 11500,
                'stock' => 3,
                'description' => 'Modern ATX case with clean design and strong airflow.',
                'form_factor' => 'ATX',
                'case_clearance_mm' => 365,
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            $brand = Brand::where('name', $productData['brand'])->first();

            Product::updateOrCreate(
                ['slug' => Str::slug($productData['name'])],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $productData['name'],
                    'model' => $productData['model'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'image_url' => 'https://placehold.co/600x400/0D1A2F/09DBC7?text=' . urlencode($productData['name']),
                    'description' => $productData['description'],
                    'socket_type' => $productData['socket_type'] ?? null,
                    'ram_type' => $productData['ram_type'] ?? null,
                    'form_factor' => $productData['form_factor'] ?? null,
                    'wattage' => $productData['wattage'] ?? null,
                    'storage_type' => $productData['storage_type'] ?? null,
                    'gpu_length_mm' => $productData['gpu_length_mm'] ?? null,
                    'case_clearance_mm' => $productData['case_clearance_mm'] ?? null,
                    'is_active' => true,
                ]
            );
        }
    }
}