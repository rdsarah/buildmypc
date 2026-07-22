<?php

namespace App\Services;

use App\Models\Build;

class CompatibilityService
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

    public function analyze(Build $build): array
    {
        $build->loadMissing([
            'items.product.category',
            'items.product.brand',
            'items.category',
        ]);

        $productsByCategory = $build->items
            ->mapWithKeys(fn ($item) => [$item->category->name => $item->product]);

        $missingCategories = collect($this->requiredCategories)
            ->reject(fn ($category) => $productsByCategory->has($category))
            ->values()
            ->all();

        $totalPrice = $productsByCategory->sum(fn ($product) => (float) $product->price);
        $estimatedWattage = $this->estimateWattage($productsByCategory);

        $checks = [];

        $cpu = $productsByCategory->get('CPU');
        $motherboard = $productsByCategory->get('Motherboard');
        $ram = $productsByCategory->get('RAM');
        $gpu = $productsByCategory->get('GPU');
        $psu = $productsByCategory->get('PSU');
        $case = $productsByCategory->get('Casing');

        if ($cpu && $motherboard) {
            $checks[] = $this->checkCpuMotherboardSocket($cpu, $motherboard);
        }

        if ($ram && $motherboard) {
            $checks[] = $this->checkRamMotherboardType($ram, $motherboard);
        }

        if ($motherboard && $case) {
            $checks[] = $this->checkMotherboardCaseFit($motherboard, $case);
        }

        if ($gpu && $case) {
            $checks[] = $this->checkGpuCaseClearance($gpu, $case);
        }

        if ($psu && ($cpu || $gpu)) {
            $checks[] = $this->checkPsuWattage($psu, $estimatedWattage);
        }

        $hasFailure = collect($checks)->contains(fn ($check) => $check['status'] === 'fail');
        $hasWarning = collect($checks)->contains(fn ($check) => $check['status'] === 'warning');

        if (count($missingCategories) > 0) {
            $overallStatus = 'incomplete';
            $overallMessage = 'Select all required components to complete the build.';
        } elseif ($hasFailure) {
            $overallStatus = 'incompatible';
            $overallMessage = 'This build has compatibility issues.';
        } elseif ($hasWarning) {
            $overallStatus = 'needs_review';
            $overallMessage = 'This build is mostly ready, but some specifications could not be fully verified.';
        } else {
            $overallStatus = 'compatible';
            $overallMessage = 'This build appears compatible based on available specifications.';
        }

        return [
            'productsByCategory' => $productsByCategory,
            'missingCategories' => $missingCategories,
            'selectedCount' => $productsByCategory->count(),
            'requiredCount' => count($this->requiredCategories),
            'totalPrice' => $totalPrice,
            'estimatedWattage' => $estimatedWattage,
            'checks' => $checks,
            'overallStatus' => $overallStatus,
            'overallMessage' => $overallMessage,
        ];
    }

    private function checkCpuMotherboardSocket($cpu, $motherboard): array
    {
        if (!$cpu->socket_type || !$motherboard->socket_type) {
            return $this->result(
                'CPU and Motherboard Socket',
                'warning',
                'Socket information is missing for one of the selected components.'
            );
        }

        if ($this->sameText($cpu->socket_type, $motherboard->socket_type)) {
            return $this->result(
                'CPU and Motherboard Socket',
                'pass',
                "CPU socket {$cpu->socket_type} matches motherboard socket {$motherboard->socket_type}."
            );
        }

        return $this->result(
            'CPU and Motherboard Socket',
            'fail',
            "CPU socket {$cpu->socket_type} does not match motherboard socket {$motherboard->socket_type}."
        );
    }

    private function checkRamMotherboardType($ram, $motherboard): array
    {
        if (!$ram->ram_type || !$motherboard->ram_type) {
            return $this->result(
                'RAM and Motherboard Memory Type',
                'warning',
                'RAM type information is missing for one of the selected components.'
            );
        }

        if ($this->sameText($ram->ram_type, $motherboard->ram_type)) {
            return $this->result(
                'RAM and Motherboard Memory Type',
                'pass',
                "RAM type {$ram->ram_type} matches motherboard memory type {$motherboard->ram_type}."
            );
        }

        return $this->result(
            'RAM and Motherboard Memory Type',
            'fail',
            "RAM type {$ram->ram_type} does not match motherboard memory type {$motherboard->ram_type}."
        );
    }

    private function checkMotherboardCaseFit($motherboard, $case): array
    {
        if (!$motherboard->form_factor || !$case->form_factor) {
            return $this->result(
                'Motherboard and Case Form Factor',
                'warning',
                'Form factor information is missing for one of the selected components.'
            );
        }

        if ($this->caseSupportsMotherboard($case->form_factor, $motherboard->form_factor)) {
            return $this->result(
                'Motherboard and Case Form Factor',
                'pass',
                "{$motherboard->form_factor} motherboard fits inside {$case->form_factor} case."
            );
        }

        return $this->result(
            'Motherboard and Case Form Factor',
            'fail',
            "{$motherboard->form_factor} motherboard may not fit inside {$case->form_factor} case."
        );
    }

    private function checkGpuCaseClearance($gpu, $case): array
    {
        if (!$gpu->gpu_length_mm || !$case->case_clearance_mm) {
            return $this->result(
                'GPU and Case Clearance',
                'warning',
                'GPU length or case clearance information is missing.'
            );
        }

        if ((int) $gpu->gpu_length_mm <= (int) $case->case_clearance_mm) {
            return $this->result(
                'GPU and Case Clearance',
                'pass',
                "GPU length {$gpu->gpu_length_mm}mm fits within case clearance {$case->case_clearance_mm}mm."
            );
        }

        return $this->result(
            'GPU and Case Clearance',
            'fail',
            "GPU length {$gpu->gpu_length_mm}mm exceeds case clearance {$case->case_clearance_mm}mm."
        );
    }

    private function checkPsuWattage($psu, int $estimatedWattage): array
    {
        if (!$psu->wattage || $estimatedWattage <= 0) {
            return $this->result(
                'Power Supply Wattage',
                'warning',
                'PSU wattage or build wattage information is missing.'
            );
        }

        if ((int) $psu->wattage >= $estimatedWattage) {
            return $this->result(
                'Power Supply Wattage',
                'pass',
                "PSU wattage {$psu->wattage}W is enough for estimated build wattage {$estimatedWattage}W."
            );
        }

        return $this->result(
            'Power Supply Wattage',
            'fail',
            "PSU wattage {$psu->wattage}W is lower than estimated build wattage {$estimatedWattage}W."
        );
    }

    private function estimateWattage($productsByCategory): int
    {
        $cpuWattage = (int) ($productsByCategory->get('CPU')->wattage ?? 0);
        $gpuWattage = (int) ($productsByCategory->get('GPU')->wattage ?? 0);

        $systemAllowance = ($cpuWattage > 0 || $gpuWattage > 0) ? 100 : 0;

        return $cpuWattage + $gpuWattage + $systemAllowance;
    }

    private function caseSupportsMotherboard(string $caseFormFactor, string $motherboardFormFactor): bool
    {
        $supportMap = [
            'EATX' => ['EATX', 'ATX', 'Micro-ATX', 'Mini-ITX'],
            'ATX' => ['ATX', 'Micro-ATX', 'Mini-ITX'],
            'Micro-ATX' => ['Micro-ATX', 'Mini-ITX'],
            'Mini-ITX' => ['Mini-ITX'],
        ];

        return in_array(
            $this->normalizeFormFactor($motherboardFormFactor),
            $supportMap[$this->normalizeFormFactor($caseFormFactor)] ?? [],
            true
        );
    }

    private function normalizeFormFactor(string $value): string
    {
        return match (trim($value)) {
            'Micro ATX', 'MicroATX', 'mATX', 'MATX' => 'Micro-ATX',
            'Mini ITX', 'MiniITX', 'mITX' => 'Mini-ITX',
            'E-ATX' => 'EATX',
            default => trim($value),
        };
    }

    private function sameText(string $first, string $second): bool
    {
        return strtolower(trim($first)) === strtolower(trim($second));
    }

    private function result(string $title, string $status, string $message): array
    {
        return [
            'title' => $title,
            'status' => $status,
            'message' => $message,
        ];
    }
}