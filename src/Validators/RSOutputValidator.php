<?php

namespace Dgtlinf\SalaryCalculator\Validators;

class RSOutputValidator extends BaseOutputValidator
{
    protected array $countryKeys = [
        'salary.contributions_base',
        'salary.employee_contributions.total',
        'salary.employer_contributions.total',
        'salary.income_tax.amount',
    ];

    public function getCountryCode(): string
    {
        return 'RS';
    }
}
