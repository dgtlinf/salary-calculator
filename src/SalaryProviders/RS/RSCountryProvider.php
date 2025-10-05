<?php

namespace Dgtlinf\SalaryCalculator\SalaryProviders\RS;

use Dgtlinf\SalaryCalculator\SalaryCalculator;
use Dgtlinf\SalaryCalculator\Traits\RoundingTrait;

class RSCountryProvider extends SalaryCalculator
{
    use RoundingTrait;

    protected string $countryCode = 'RS';

    public function fromGross(float $gross): array
    {
        $ctx = $this->getContext()->toArray();

        // Adjust gross
        $gross = $this->applySickLeaveAdjustment($gross, $ctx);

        $contribBase = $this->getContributionBase($gross);
        $base = $this->getTaxBase($gross);
        $incomeTax = $base * $this->taxTable['tax_rate'];

        $employeeContrib = $this->getEmployeeContributions($contribBase);
        $employerContrib = $this->getEmployerContributions($contribBase);

        $net = $gross - $employeeContrib['total'] - $incomeTax;
        $totalCost = $gross + $employerContrib['total'];

        $grossItems = $this->getGrossItems($gross, $ctx);

        return [
            'salary' => [
                'gross' => [
                    'items' => $grossItems,
                    'total' => $this->round2($gross)
                ],
                'contributions_base' => $this->round2($contribBase),
                'income_tax' => [
                    'base' => $this->round2($base),
                    'amount' => $this->round2($incomeTax),
                ],
                'employee_contributions' => $this->roundArray($employeeContrib),
                'net_salary' => $this->round2($net),
                'employer_contributions' => $this->roundArray($employerContrib),
                'total_salary_cost' => $this->round2($totalCost),
            ],
            'context' => $ctx,
            'tax_table' => $this->taxTable
        ];
    }

    public function fromNet(float $net): array
    {
        $grossCalc = ($net - ($this->taxTable['non_taxable_limit'] * $this->taxTable['tax_rate'])) /
            (1 - (
                    $this->taxTable['employee']['pension_rate'] +
                    $this->taxTable['employee']['health_rate'] +
                    $this->taxTable['employee']['unemployment_rate'] +
                    $this->taxTable['tax_rate']
                ));

        if ($grossCalc <= $this->taxTable['min_contribution_base']) {
            $gross = (($net - ($this->taxTable['non_taxable_limit'] * $this->taxTable['tax_rate'])) +
                    ($this->taxTable['min_contribution_base'] * (
                            $this->taxTable['employee']['pension_rate'] +
                            $this->taxTable['employee']['health_rate'] +
                            $this->taxTable['employee']['unemployment_rate']
                        ))) / (1 - $this->taxTable['tax_rate']);
        } elseif ($grossCalc >= $this->taxTable['max_contribution_base']) {
            $gross = (($net - ($this->taxTable['non_taxable_limit'] * $this->taxTable['tax_rate'])) +
                    ($this->taxTable['max_contribution_base'] * (
                            $this->taxTable['employee']['pension_rate'] +
                            $this->taxTable['employee']['health_rate'] +
                            $this->taxTable['employee']['unemployment_rate']
                        ))) / (1 - $this->taxTable['tax_rate']);
        } else {
            $gross = $grossCalc;
        }

        return $this->fromGross($gross);
    }

    // === Private helpers ===

    private function applySickLeaveAdjustment(float $gross, array $ctx): float
    {
        if ( $ctx['hours']['sick_leave_hours'] < 1 ) {
            return $gross;
        }

        $totalHours = $ctx['hours']['total_hours'];
        $sickHours  = $ctx['hours']['sick_leave_hours'];

        $sickRate  = $ctx['sick_leave_full_pay'] ? 1.0 : $this->taxTable['sick_leave_reduction_rate'];
        $workHours = $ctx['hours']['working_hours']; // only working hours

        $portionWork = $workHours / $totalHours;
        $portionSick = $sickHours / $totalHours;

        return
            ($gross * $portionWork) +
            ($gross * $portionSick * $sickRate);
    }

    private function getSickLeaveItem(float $gross, array $ctx): ?array
    {
        $sickHours = $ctx['hours']['sick_leave_hours'];
        if ($sickHours <= 0) {
            return [
                'units' => 0,
                'unit' => '_hours',
                'basis' => 1,
                'per_unit' => 0,
                'amount' => 0
            ];
        }

        $sickRate = $ctx['sick_leave_full_pay'] ? 1.0 : $this->taxTable['sick_leave_reduction_rate'];

        $hourlyGross = $this->resolveAverageHourlyRate($ctx, $gross);

        $amount = $hourlyGross * $sickHours * $sickRate;

        return [
            'units' => $sickHours,
            'unit' => '_hours',
            'basis' => $sickRate,
            'per_unit' => $hourlyGross * $sickRate,
            'amount' => $amount,
        ];
    }

    private function setGrossItem(
        ?string $unit = null,
        float|int|null $units = null,
        float|int|null $per_unit = null,
        float|int|null $basis = null,
        float|int|null $amount = null
    ): array {
        $basis = $basis ?? 1;

        $round = method_exists($this, 'round2') ? fn($v) => $this->round2($v) : fn($v) => $v;

        return array_filter([
            'units' => $units,
            'unit' => $unit,
            'per_unit' => $per_unit !== null ? $round($per_unit) : null,
            'basis' => $basis,
            'amount' => $amount !== null ? $round($amount) : null,
        ], fn($v) => $v !== null);
    }

    private function getGrossItems(float $gross, array $ctx): array
    {
        $fullHours = $ctx['hours']['total_hours'];
        $workingHours = $ctx['hours']['working_hours'];

        $vacationAllowance = $this->taxTable['vacation_allowance_per_month'];
        $mealPerDay = $this->taxTable['meal_allowance_per_month'] / ($fullHours / 8);
        $mealAmount = $mealPerDay * ($workingHours / 8);

        $seniority = $this->getSeniorityAllowance(
            $gross,
            $mealAmount,
            $vacationAllowance,
            $ctx['years_in_service']
        );

        $sickLeaveItem = $this->getSickLeaveItem($gross, $ctx);

        $avgRate = $this->resolveAverageHourlyRate($ctx, $gross);
        $vacationAmount = $ctx['hours']['vacation_hours'] * $avgRate;

        $totalAllowances = $seniority + $mealAmount + $vacationAllowance;
        $regularHourlyRate = ($gross - $totalAllowances - $vacationAmount - $sickLeaveItem['amount']) / $workingHours;

        return [
            'regular_work' => $this->setGrossItem(
                unit: '_hours',
                units: $workingHours,
                per_unit: $regularHourlyRate,
                basis: 1,
                amount: $regularHourlyRate * $workingHours,
            ),
            'seniority_allowance' => $this->setGrossItem(
                unit: '_hours',
                units: $workingHours,
                per_unit: $regularHourlyRate,
                basis: $ctx['years_in_service'] * $this->taxTable['seniority_allowance_rate_per_year'],
                amount: $seniority,
            ),
            'meal_allowance' => $this->setGrossItem(
                unit: '_days',
                units: $workingHours / 8,
                per_unit: $mealPerDay,
                basis: 1,
                amount: $mealAmount,
            ),
            'vacation_allowance' => $this->setGrossItem(
                units: 0,
                per_unit: 0,
                amount: $vacationAllowance,
            ),
            'vacation' => $this->setGrossItem(
                unit: '_hours',
                units: $ctx['hours']['vacation_hours'],
                per_unit: $avgRate,
                basis: 1,
                amount: $vacationAmount,
            ),
            'sick_leave' => $this->setGrossItem(
                unit:  $sickLeaveItem['unit'],
                units: $sickLeaveItem['units'],
                per_unit: $sickLeaveItem['per_unit'],
                basis: $sickLeaveItem['basis'],
                amount: $sickLeaveItem['amount'],
            ),
        ];
    }

    private function getSeniorityAllowance(float $gross, float $meal, float $holiday, int $years): float
    {
        $rate = $years * $this->taxTable['seniority_allowance_rate_per_year'];
        $base = ($gross - ($meal + $holiday)) / (1 + $rate);
        return $base * $rate;
    }

    private function getContributionBase(float $gross): float
    {
        return max(
            $this->taxTable['min_contribution_base'],
            min($gross, $this->taxTable['max_contribution_base'])
        );
    }

    private function getTaxBase(float $gross): float
    {
        return $gross - $this->taxTable['non_taxable_limit'];
    }

    private function getEmployeeContributions(float $base): array
    {
        return [
            'pension_contributions' => $p = $base * $this->taxTable['employee']['pension_rate'],
            'healthcare_contributions' => $h = $base * $this->taxTable['employee']['health_rate'],
            'unemployment_contributions' => $u = $base * $this->taxTable['employee']['unemployment_rate'],
            'total' => $p + $h + $u,
        ];
    }

    private function getEmployerContributions(float $base): array
    {
        return [
            'pension_contributions' => $p = $base * $this->taxTable['employer']['pension_rate'],
            'healthcare_contributions' => $h = $base * $this->taxTable['employer']['health_rate'],
            'unemployment_contributions' => $u = $base * $this->taxTable['employer']['unemployment_rate'],
            'total' => $p + $h + $u,
        ];
    }
}
