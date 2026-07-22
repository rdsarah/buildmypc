<?php

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportPcPartDataset extends Command
{
    protected $signature = 'pcparts:import 
                            {--limit=20 : Number of products to import per category}
                            {--rate=120 : Fixed USD to BDT conversion rate}';

    protected $description = 'Import PC component data from an open-source PC part dataset';

    private array $sources = [
        'cpu' => [
            'category' => 'CPU',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/cpu.csv',
        ],
        'motherboard' => [
            'category' => 'Motherboard',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/motherboard.csv',
        ],
        'memory' => [
            'category' => 'RAM',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/memory.csv',
        ],
        'video-card' => [
            'category' => 'GPU',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/video-card.csv',
        ],
        'internal-hard-drive' => [
            'category' => 'Storage',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/internal-hard-drive.csv',
        ],
        'power-supply' => [
            'category' => 'PSU',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/power-supply.csv',
        ],
        'case' => [
            'category' => 'Casing',
            'url' => 'https://raw.githubusercontent.com/docyx/pc-part-dataset/main/data/csv/case.csv',
        ],
    ];

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $exchangeRate = (float) $this->option('rate');

        $this->info('Starting PC part dataset import...');
        $this->info('Fixed exchange rate: 1 USD = Tk ' . $exchangeRate);

        foreach ($this->sources as $partType => $source) {
            $this->newLine();
            $this->info('Importing ' . $source['category'] . '...');

            try {
                $rows = $this->downloadCsvRows($source['url']);
                $this->importRows($rows, $source['category'], $partType, $limit, $exchangeRate);
            } catch (\Throwable $error) {
                $this->error('Failed to import ' . $source['category'] . ': ' . $error->getMessage());
            }
        }

        $this->newLine();
        $this->info('Dataset import completed.');

        return self::SUCCESS;
    }

    private function downloadCsvRows(string $url): array
    {
        $response = Http::timeout(120)->get($url);

        if (!$response->successful()) {
            throw new \Exception('Could not download CSV. HTTP status: ' . $response->status());
        }

        $csvContent = $response->body();

        $handle = fopen('php://temp', 'r+');
        fwrite($handle, $csvContent);
        rewind($handle);

        $headers = fgetcsv($handle);

        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = array_map(fn ($header) => trim((string) $header), $headers);

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) {
                continue;
            }

            $rows[] = array_combine($headers, $data);
        }

        fclose($handle);

        return $rows;
    }

    private function importRows(array $rows, string $categoryName, string $partType, int $limit, float $exchangeRate): void
    {
        $category = Category::firstOrCreate(
            ['slug' => Str::slug($categoryName)],
            ['name' => $categoryName]
        );

        $imported = 0;

        foreach ($rows as $row) {
            if ($imported >= $limit) {
                break;
            }

            $name = $this->cleanValue($row['name'] ?? null);
            $priceUsd = $this->cleanPrice($row['price'] ?? null);

            if (!$name || !$priceUsd) {
                continue;
            }

            $brandName = $this->guessBrandFromName($name);

            $brand = Brand::firstOrCreate(
                ['slug' => Str::slug($brandName)],
                ['name' => $brandName]
            );

            $priceBdt = round($priceUsd * $exchangeRate);

            Product::updateOrCreate(
                [
                    'slug' => Str::slug($categoryName . '-' . $name),
                ],
                [
                    'category_id' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $name,
                    'model' => $this->cleanValue($row['model'] ?? null),
                    'price' => $priceBdt,
                    'original_price_usd' => $priceUsd,
                    'exchange_rate_used' => $exchangeRate,
                    'stock' => $this->generateDemoStock($name),
                    'image_url' => 'https://placehold.co/600x400/0D1A2F/09DBC7?text=' . urlencode($name),
                    'description' => $this->buildDescription($name, $categoryName, $row),
                    'socket_type' => $this->extractSocketType($row, $partType),
                    'ram_type' => $this->extractRamType($row, $partType),
                    'form_factor' => $this->extractFormFactor($row, $partType),
                    'wattage' => $this->extractWattage($row, $partType),
                    'storage_type' => $this->extractStorageType($row, $partType),
                    'gpu_length_mm' => $this->extractGpuLength($row, $partType),
                    'case_clearance_mm' => null,
                    'source_dataset' => 'docyx/pc-part-dataset',
                    'source_part_type' => $partType,
                    'is_active' => true,
                ]
            );

            $imported++;
        }

        $this->info($imported . ' ' . $categoryName . ' products imported.');
    }

    private function cleanValue(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || strtolower($value) === 'null') {
            return null;
        }

        return $value;
    }

    private function cleanPrice(?string $value): ?float
    {
        $value = $this->cleanValue($value);

        if (!$value) {
            return null;
        }

        $value = preg_replace('/[^\d.]/', '', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    private function guessBrandFromName(string $name): string
    {
        $knownBrands = [
            'AMD',
            'Intel',
            'MSI',
            'Gigabyte',
            'ASUS',
            'Corsair',
            'Kingston',
            'G.Skill',
            'Samsung',
            'Seagate',
            'Western Digital',
            'WD',
            'Cooler Master',
            'DeepCool',
            'NZXT',
            'EVGA',
            'Zotac',
            'Sapphire',
            'PowerColor',
            'Crucial',
            'Teamgroup',
            'Thermaltake',
            'Antec',
            'Silverstone',
            'Fractal Design',
        ];

        foreach ($knownBrands as $brand) {
            if (Str::startsWith(strtolower($name), strtolower($brand))) {
                return $brand;
            }
        }

        return Str::before($name, ' ') ?: 'Unknown';
    }

    private function generateDemoStock(string $name): int
    {
        $number = abs(crc32($name)) % 20;

        if ($number <= 2) {
            return 0;
        }

        return $number;
    }

    private function buildDescription(string $name, string $categoryName, array $row): string
    {
        $parts = [
            $name . ' imported from an open-source PC component dataset.',
            'Category: ' . $categoryName . '.',
        ];

        if (!empty($row['price'])) {
            $parts[] = 'Original dataset price is stored in USD and converted to BDT for display.';
        }

        return implode(' ', $parts);
    }

    private function extractSocketType(array $row, string $partType): ?string
    {
        if ($partType === 'motherboard') {
            return $this->cleanValue($row['socket'] ?? null);
        }

        return null;
    }

    private function extractRamType(array $row, string $partType): ?string
    {
        if ($partType === 'memory') {
            $speed = $this->cleanValue($row['speed'] ?? null);

            if ($speed && preg_match('/DDR\s?(\d)/i', $speed, $matches)) {
                return 'DDR' . $matches[1];
            }

            if ($speed && preg_match('/\[(\d)/', $speed, $matches)) {
                return 'DDR' . $matches[1];
            }
        }

        return null;
    }

    private function extractFormFactor(array $row, string $partType): ?string
    {
        if ($partType === 'motherboard') {
            return $this->cleanValue($row['form_factor'] ?? null);
        }

        if ($partType === 'case') {
            return $this->cleanValue($row['type'] ?? null);
        }

        return null;
    }

    private function extractWattage(array $row, string $partType): ?int
    {
        $value = null;

        if ($partType === 'cpu') {
            $value = $row['tdp'] ?? null;
        }

        if ($partType === 'power-supply') {
            $value = $row['wattage'] ?? null;
        }

        if (!$value) {
            return null;
        }

        $value = preg_replace('/[^\d]/', '', (string) $value);

        return is_numeric($value) ? (int) $value : null;
    }

    private function extractStorageType(array $row, string $partType): ?string
    {
        if ($partType !== 'internal-hard-drive') {
            return null;
        }

        $type = $this->cleanValue($row['type'] ?? null);
        $interface = $this->cleanValue($row['interface'] ?? null);

        if ($type && str_contains(strtolower($type), 'ssd')) {
            if ($interface && str_contains(strtolower($interface), 'pcie')) {
                return 'NVMe';
            }

            return 'SSD';
        }

        if ($type) {
            return 'HDD';
        }

        return null;
    }

    private function extractGpuLength(array $row, string $partType): ?int
    {
        if ($partType !== 'video-card') {
            return null;
        }

        $length = $row['length'] ?? null;

        if (!$length) {
            return null;
        }

        $length = preg_replace('/[^\d]/', '', (string) $length);

        return is_numeric($length) ? (int) $length : null;
    }
}