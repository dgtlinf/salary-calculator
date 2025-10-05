<?php

namespace Dgtlinf\SalaryCalculator\Validators;

use Dgtlinf\SalaryCalculator\Contracts\SalaryOutputValidator;
use Dgtlinf\SalaryCalculator\Exceptions\InvalidSalaryStructureException;

abstract class BaseOutputValidator implements SalaryOutputValidator
{
    /**
     * Core required keys for all countries.
     */
    protected array $baseKeys = [
        'salary.gross.items',
        'salary.gross.total',
        'salary.net_salary',
        'salary.total_salary_cost',
        'context',
        'tax_table',
    ];

    /**
     * Additional country-specific keys.
     */
    protected array $countryKeys = [];

    public function validate(array $salaryOutput): void
    {
        $required = array_merge($this->baseKeys, $this->countryKeys);

        foreach ($required as $path) {
            if (data_get($salaryOutput, $path) === null) {
                throw new InvalidSalaryStructureException(
                    sprintf(
                        'Missing required key "%s" in salary output for country %s',
                        $path,
                        $this->getCountryCode()
                    )
                );
            }
        }
    }

    public function expectedKeys(): array
    {
        return array_merge($this->baseKeys, $this->countryKeys);
    }

    abstract public function getCountryCode(): string;
}
