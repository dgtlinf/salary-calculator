<?php

namespace Dgtlinf\SalaryCalculator;

use Dgtlinf\SalaryCalculator\Contracts\SalaryOutputValidator;
use Dgtlinf\SalaryCalculator\Contracts\SalaryProviderInterface;
use Dgtlinf\SalaryCalculator\Models\SalaryContext;
use Dgtlinf\SalaryCalculator\Validators\BaseOutputValidator;
use RuntimeException;

abstract class SalaryCalculator implements SalaryProviderInterface
{
    protected array $taxTable = [];
    protected string $countryCode = 'RS';

    protected SalaryContext $context;

    public function __construct(array $taxTable, SalaryContext $context)
    {
        $this->taxTable = $taxTable;
        $this->context = $context;
    }

    abstract public function fromGross(float $gross): array;
    abstract public function fromNet(float $net): array;

    protected function resolveAverageHourlyRate(array $ctx, float $gross): float
    {
        $totalHours = $ctx['hours']['total_hours'] ?? 0;

        if ($totalHours <= 0) {
            return 0.0;
        }

        return $ctx['avg_gross_hourly_rate_last_12_months']
            ?? ($gross / $totalHours);
    }

    public function getCountryCode(): string
    {
        return $this->context->countryCode;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->taxTable['currency_code'] ?? null;
    }

    public function getCurrencySymbol(): ?string
    {
        return $this->taxTable['currency_symbol'] ?? null;
    }

    public function getTaxTable(): array
    {
        return $this->taxTable;
    }

    public function getTaxYear(): int
    {
        return $this->context->year;
    }

    public function getContext(): SalaryContext
    {
        return $this->context;
    }

    public function validateOutput(array $output): void
    {
        $country = strtoupper($this->getContext()->country ?? config('salary-calculator.default_country'));
        $validators = config('salary-calculator.validators', []);

        $strict = config('salary-calculator.behavior.strict_validation', false);
        $fallback = config('salary-calculator.behavior.fallback_to_base_validator', true);

        $validatorClass = $validators[$country] ?? null;

        if (!$validatorClass) {
            if ($strict) {
                throw new RuntimeException("No output validator registered for country: {$country}");
            }

            if ($fallback) {
                $validatorClass = BaseOutputValidator::class;
            }
        }

        if (!$validatorClass) {
            // If neither strict nor fallback are allowed, just skip silently.
            return;
        }

        /** @var SalaryOutputValidator $validator */
        $validator = new $validatorClass();
        $validator->validate($output);
    }
}
