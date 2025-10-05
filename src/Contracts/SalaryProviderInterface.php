<?php

namespace Dgtlinf\SalaryCalculator\Contracts;

use Dgtlinf\SalaryCalculator\Models\SalaryContext;

interface SalaryProviderInterface
{
    public function fromGross(float $gross): array;
    public function fromNet(float $net): array;
    public function getCountryCode(): string;
    public function getCurrencyCode(): ?string;
    public function getCurrencySymbol(): ?string;
    public function getTaxTable(): array;
    public function getTaxYear(): int;
    public function getContext(): SalaryContext;
    public function validateOutput(array $output): void;
}
