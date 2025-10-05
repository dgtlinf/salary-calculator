<?php

namespace Dgtlinf\SalaryCalculator;

use Dgtlinf\SalaryCalculator\Models\SalaryContext;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

class SalaryCalculatorManager
{
    protected array $providers = [];

    /**
     * Register a salary provider for a specific country code.
     */
    public function register(string $countryCode, string $providerClass): void
    {
        // Ensure the provider class extends the base SalaryCalculator
        if (!is_subclass_of($providerClass, \Dgtlinf\SalaryCalculator\SalaryCalculator::class)) {
            throw new InvalidArgumentException("Provider {$providerClass} must extend SalaryCalculator");
        }

        $this->providers[strtoupper($countryCode)] = $providerClass;
    }

    /**
     * Resolve the correct salary calculator instance for a given context.
     */
    public function for(SalaryContext $context): SalaryCalculator
    {
        $countryCode = strtoupper($context->countryCode);
        $year = $context->year;

        if (!isset($this->providers[$countryCode])) {
            throw new InvalidArgumentException("No provider registered for {$countryCode}");
        }

        $taxTable = $this->loadTaxTable($countryCode, $year);
        $providerClass = $this->providers[$countryCode];

        return new $providerClass($taxTable, $context);
    }

    /**
     * Load the tax table for a given country and year.
     * Falls back to the latest available if not found.
     */
    protected function loadTaxTable(string $countryCode, int $year): array
    {
        // Try to load from custom tax_tables_path first
        $customPath = config('salary-calculator.tax_tables_path') . "/{$countryCode}/{$year}.php";
        if (File::exists($customPath)) {
            return include $customPath;
        }

        // Otherwise, fallback to package defaults
        $path = __DIR__ . "/SalaryProviders/{$countryCode}/TaxTables/{$year}.php";
        if (File::exists($path)) {
            return include $path;
        }

        // Fallback to latest file if not found for given year
        $files = collect(File::files(__DIR__ . "/SalaryProviders/{$countryCode}/TaxTables"))
            ->sortDesc()
            ->values();

        return $files->isNotEmpty()
            ? include $files->first()->getPathname()
            : [];
    }
}
