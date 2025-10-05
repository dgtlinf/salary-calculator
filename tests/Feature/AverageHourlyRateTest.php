<?php

use Dgtlinf\SalaryCalculator\Facades\AverageHourlyRate;

it('calculates average hourly rate for RS', function () {
    $months = [
        ['date' => '2025-01-01', 'gross' => 420000],
        ['date' => '2025-02-01', 'gross' => 410000],
        ['date' => '2025-03-01', 'gross' => 435000],
    ];

    $avg = AverageHourlyRate::calculate($months, 'RS');

    expect($avg)->toBeFloat()->toBeGreaterThan(2000)->toBeLessThan(3000);
});
