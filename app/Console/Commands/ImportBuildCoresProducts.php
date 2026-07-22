<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportBuildCoresProducts extends Command
{
    protected $signature = 'buildcores:import 
                            {--path= : Full path to buildcores-open-db folder}
                            {--limit=15 : Number of products to import per category}';

    protected $description = 'Import PC component data from BuildCores OpenDB into the local products table';

    private array $categoryMap = [
        'CPU' => 'CPU',
        'Motherboard' => 'Motherboard',
        'RAM' => 'RAM',
        'GPU' => 'GPU',
        'Storage' => 'Storage',
        'PSU' => 'PSU',
        'PCCase' => 'Casing',
    ];

    public function handle(): int
    {
        $basePath = $this->option('path');

        if (!$basePath) {
            $this->error('Please provide the BuildCores path using --path=');
            return self::FAILURE;
        }

        $openDbPath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'open-db';

        if (!File::exists($openDbPath)) {
            $this->error("Could not find open-db folder at: {$openDbPath}");
            return self::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $totalImported = 0;

        foreach ($this->categoryMap as $sourceCategory => $localCategoryName) {
            $categoryPath = $openDbPath . DIRECTORY_SEPARATOR . $sourceCategory;

            if (!File::exists($categoryPath)) {
                $this->warn("Skipped {$sourceCategory}: folder not found.");
                continue;
            }

            $this->info("Importing {$sourceCategory} as {$localCategoryName}...");

            $files = collect(File::files($categoryPath))
                ->filter(fn ($file) => strtolower($file->getExtension()) === 'json')
                ->sortBy(fn ($file) => $file->getFilename())
                ->values();

            if ($limit > 0) {
                $files = $files->take($limit);
            }

            $categoryImported = 0;

            foreach ($files as $file) {
                $json = File::get($file->getPathname());
                $data = json_decode($json, true);

                if (!is_array($data)) {
                    $this->warn("Invalid JSON skipped: " . $file->getFilename());
                    continue;
                }

                $mappedProduct = $this->mapProduct(
                    $data,
                    $sourceCategory,
                    $localCategoryName,
                    $file->getFilename()
                );

                if (!$mappedProduct) {
                    $this->warn("Skipped product because name was missing: " . $file->getFilename());
                    continue;
                }

                $category = Category::firstOrCreate(
                    ['slug' => Str::slug($localCategoryName)],
                    ['name' => $localCategoryName]
                );

                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($mappedProduct['brand'])],
                    ['name' => $mappedProduct['brand']]
                );

                Product::updateOrCreate(
                    [
                        'source_name' => 'BuildCores OpenDB',
                        'source_id' => $mappedProduct['source_id'],
                    ],
                    [
                        'category_id' => $category->id,
                        'brand_id' => $brand->id,
                        'name' => $mappedProduct['name'],
                        'slug' => $mappedProduct['slug'],
                        'model' => $mappedProduct['model'],
                        'price' => $mappedProduct['price'],
                        'stock' => $mappedProduct['stock'],
                        'image_url' => $mappedProduct['image_url'],
                        'description' => $mappedProduct['description'],
                        'socket_type' => $mappedProduct['socket_type'],
                        'ram_type' => $mappedProduct['ram_type'],
                        'form_factor' => $mappedProduct['form_factor'],
                        'wattage' => $mappedProduct['wattage'],
                        'storage_type' => $mappedProduct['storage_type'],
                        'gpu_length_mm' => $mappedProduct['gpu_length_mm'],
                        'case_clearance_mm' => $mappedProduct['case_clearance_mm'],
                        'is_active' => true,
                        'source_category' => $sourceCategory,
                        'specs_json' => $data,
                    ]
                );

                $categoryImported++;
                $totalImported++;
            }

            $this->line("Imported {$categoryImported} products from {$sourceCategory}.");
        }

        $this->newLine();
        $this->info("Done. Total imported products: {$totalImported}");

        return self::SUCCESS;
    }

    private function mapProduct(
        array $data,
        string $sourceCategory,
        string $localCategoryName,
        string $filename
    ): ?array {
        $name = $this->get($data, 'metadata.name')
            ?? $data['name']
            ?? null;

        if (!$name) {
            return null;
        }

        $brand = $this->get($data, 'metadata.manufacturer')
            ?? $data['manufacturer']
            ?? $data['brand']
            ?? $data['chipset_manufacturer']
            ?? 'Unknown';

        $sourceId = $data['opendb_id'] ?? pathinfo($filename, PATHINFO_FILENAME);

        $model = $this->get($data, 'metadata.variant')
            ?? $this->get($data, 'metadata.series')
            ?? $data['series']
            ?? $data['model']
            ?? $name;

        $socketType = null;
        $ramType = null;
        $formFactor = null;
        $wattage = null;
        $storageType = null;
        $gpuLengthMm = null;
        $caseClearanceMm = null;

        if ($sourceCategory === 'CPU') {
            $socketType = $this->normalizeSocket(
                $data['socket']
                ?? $this->get($data, 'specifications.socket')
                ?? null
            );

            $wattage = $this->number(
                $data['tdp']
                ?? $this->get($data, 'specifications.tdp')
                ?? null
            );
        }

        if ($sourceCategory === 'Motherboard') {
            $socketType = $this->normalizeSocket(
                $data['socket']
                ?? $this->get($data, 'cpu.socket')
                ?? null
            );

            $ramType = $this->normalizeRamType(
                $data['ram_type']
                ?? $this->get($data, 'memory.ram_type')
                ?? $this->get($data, 'memory.type')
                ?? null
            );

            $formFactor = $this->normalizeFormFactor(
                $data['form_factor']
                ?? $this->get($data, 'dimensions.form_factor')
                ?? null
            );
        }

        if ($sourceCategory === 'RAM') {
            $ramType = $this->normalizeRamType(
                $data['ram_type']
                ?? $data['type']
                ?? $this->get($data, 'memory.type')
                ?? null
            );
        }

        if ($sourceCategory === 'GPU') {
            $wattage = $this->number(
                $data['tdp']
                ?? $data['wattage']
                ?? $this->get($data, 'specifications.tdp')
                ?? null
            );

            $gpuLengthMm = $this->number(
                $data['length']
                ?? $data['length_mm']
                ?? $this->get($data, 'dimensions.length')
                ?? null
            );
        }

        if ($sourceCategory === 'Storage') {
            $storageType = $this->normalizeStorageType(
                $data['storage_type'] ?? $data['type'] ?? null,
                $data['interface'] ?? null,
                $data['nvme'] ?? null
            );
        }

        if ($sourceCategory === 'PSU') {
            $wattage = $this->number(
                $data['wattage']
                ?? $data['power']
                ?? $this->get($data, 'specifications.wattage')
                ?? null
            );

            $formFactor = $this->normalizeFormFactor(
                $data['form_factor']
                ?? null
            );
        }

        if ($sourceCategory === 'PCCase') {
            $formFactor = $this->largestSupportedFormFactor(
                $data['supported_motherboard_form_factors']
                ?? $this->get($data, 'compatibility.motherboard_form_factors')
                ?? null
            ) ?? $this->normalizeFormFactor(
                $data['form_factor']
                ?? null
            );

            $caseClearanceMm = $this->number(
                $data['max_video_card_length']
                ?? $data['gpu_clearance']
                ?? $this->get($data, 'clearance.gpu_length')
                ?? null
            );
        }

        return [
            'source_id' => $sourceId,
            'name' => $name,
            'slug' => $this->uniqueSlug($localCategoryName, $name, $sourceId),
            'brand' => $brand,
            'model' => $model,
            'price' => $this->demoPrice($localCategoryName, $name),
            'stock' => $this->demoStock($name),
            'image_url' => 'https://placehold.co/600x400/0D1A2F/09DBC7?text=' . urlencode($name),
            'description' => null,
            'socket_type' => $socketType,
            'ram_type' => $ramType,
            'form_factor' => $formFactor,
            'wattage' => $wattage,
            'storage_type' => $storageType,
            'gpu_length_mm' => $gpuLengthMm,
            'case_clearance_mm' => $caseClearanceMm,
        ];
    }

    private function get(array $data, string $path): mixed
    {
        $keys = explode('.', $path);
        $value = $data;

        foreach ($keys as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                return null;
            }

            $value = $value[$key];
        }

        return $value;
    }

    private function number(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = preg_replace('/[^0-9.]/', '', $value);
        }

        if ($value === '') {
            return null;
        }

        return (int) round((float) $value);
    }

    private function normalizeSocket(?string $socket): ?string
    {
        if (!$socket) {
            return null;
        }

        return strtoupper(str_replace(' ', '', trim($socket)));
    }

    private function normalizeRamType(?string $ramType): ?string
    {
        if (!$ramType) {
            return null;
        }

        $value = strtoupper(trim($ramType));

        if (str_contains($value, 'DDR5')) {
            return 'DDR5';
        }

        if (str_contains($value, 'DDR4')) {
            return 'DDR4';
        }

        if (str_contains($value, 'DDR3')) {
            return 'DDR3';
        }

        return $value;
    }

    private function normalizeFormFactor(?string $formFactor): ?string
    {
        if (!$formFactor) {
            return null;
        }

        $value = trim($formFactor);

        return match ($value) {
            'Micro ATX', 'MicroATX', 'mATX', 'MATX' => 'Micro-ATX',
            'Mini ITX', 'MiniITX', 'mITX' => 'Mini-ITX',
            'ATX' => 'ATX',
            'EATX', 'E-ATX' => 'EATX',
            default => $value,
        };
    }

    private function largestSupportedFormFactor(?array $supported): ?string
    {
        if (!$supported) {
            return null;
        }

        $normalized = array_map(
            fn ($item) => $this->normalizeFormFactor((string) $item),
            $supported
        );

        if (in_array('EATX', $normalized, true)) {
            return 'EATX';
        }

        if (in_array('ATX', $normalized, true)) {
            return 'ATX';
        }

        if (in_array('Micro-ATX', $normalized, true)) {
            return 'Micro-ATX';
        }

        if (in_array('Mini-ITX', $normalized, true)) {
            return 'Mini-ITX';
        }

        return $normalized[0] ?? null;
    }

    private function normalizeStorageType(?string $storageType, ?string $interface, mixed $nvme): ?string
    {
        if ($nvme === true) {
            return 'NVMe';
        }

        if ($interface && str_contains(strtolower($interface), 'nvme')) {
            return 'NVMe';
        }

        if ($interface && str_contains(strtolower($interface), 'm.2')) {
            return 'NVMe';
        }

        if (!$storageType) {
            return null;
        }

        $value = strtoupper(trim($storageType));

        if (str_contains($value, 'SSD')) {
            return 'SSD';
        }

        if (str_contains($value, 'HDD')) {
            return 'HDD';
        }

        return $value;
    }

    private function demoPrice(string $category, string $name): int
    {
        $ranges = [
            'CPU' => [12000, 65000],
            'Motherboard' => [9000, 45000],
            'RAM' => [3500, 18000],
            'GPU' => [22000, 150000],
            'Storage' => [3500, 22000],
            'PSU' => [4500, 25000],
            'Casing' => [3500, 25000],
        ];

        [$min, $max] = $ranges[$category] ?? [3000, 20000];

        $hash = crc32($category . $name);
        $price = $min + ($hash % ($max - $min));

        return (int) round($price / 100) * 100;
    }

    private function demoStock(string $name): int
    {
        return crc32($name) % 16;
    }

    private function uniqueSlug(string $category, string $name, string $sourceId): string
    {
        return Str::slug($category . '-' . $name . '-' . substr($sourceId, 0, 8));
    }
}