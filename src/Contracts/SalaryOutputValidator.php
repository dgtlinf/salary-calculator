<?php

namespace Dgtlinf\SalaryCalculator\Contracts;

interface SalaryOutputValidator
{
    public function validate(array $salaryOutput): void;
    public function getCountryCode(): string;
    public function expectedKeys(): array;
}
