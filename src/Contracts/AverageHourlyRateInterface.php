<?php

namespace Dgtlinf\SalaryCalculator\Contracts;

interface AverageHourlyRateInterface
{
    /**
     * @param array<int, array{date: \Carbon\Carbon|string, gross: float}> $monthlyGrosses
     * @return float Average gross hourly rate
     */
    public function calculate(array $monthlyGrosses): float;
}
