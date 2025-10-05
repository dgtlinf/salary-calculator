<?php

use Dgtlinf\SalaryCalculator\Facades\SalaryCalculator;
use Dgtlinf\SalaryCalculator\Models\SalaryContext;

it('calculates RS salary from gross correctly', function () {

    $context = new SalaryContext(
        2025,
        9,
        'RS',
        vacationDays: 0,
        sickDays: 0,
        sickLeaveFullPay: false,
        yearsInService: 2,
        avgHourlyRateLast12Months: null,
        employee: null,
        employer: null
    );

    $calc = SalaryCalculator::for($context);
    $result = $calc->fromGross(790729.64);

    expect($result)->toBeArray();
    expect($result)->toHaveKey('salary');
    expect($result['salary']['net_salary'])->toBe(583870.4);

});
