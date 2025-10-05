<?php

namespace Dgtlinf\SalaryCalculator\Models;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Cmixin\BusinessDay;

class SalaryContext
{
    public int $year;
    public int $month;
    public string $countryCode;
    public int $businessDays;
    public int $vacationDays;
    public int $sickDays;
    public bool $sickLeaveFullPay;
    public int $yearsInService;
    public int $workingHours;
    public ?float $avgHourlyRateLast12Months = null;
    public ?EmployeeProfile $employee = null;
    public ?EmployerProfile $employer = null;


    public function __construct(
        int $year,
        int $month,
        string $countryCode,
        int $vacationDays = 0,
        int $sickDays = 0,
        bool $sickLeaveFullPay = false,
        int $yearsInService = 0,
        ?float $avgHourlyRateLast12Months = null,
        ?EmployeeProfile $employee = null,
        ?EmployerProfile $employer = null
    ) {
        $this->year = $year;
        $this->month = $month;
        $this->countryCode = strtoupper($countryCode);
        $this->vacationDays = $vacationDays;
        $this->sickDays = $sickDays;
        $this->sickLeaveFullPay = $sickLeaveFullPay;
        $this->yearsInService = $yearsInService;
        $this->avgHourlyRateLast12Months = $avgHourlyRateLast12Months;
        $this->employee = $employee;
        $this->employer = $employer;


        // Activate BusinessDay mixin
        BusinessDay::enable(Carbon::class);
        BusinessDay::enable(CarbonImmutable::class);

        // Set region (use ISO code, ie. "rs", "de", "fr")
        Carbon::setHolidaysRegion(strtolower($countryCode));

        // calculate number of working hours in a month (no weekends and no holidays)
        $this->businessDays = $this->calculateBusinessDays($year, $month);

        // calculate the number of working hours
        $this->workingHours = $this->businessDays * 8;
    }


    protected function calculateBusinessDays(int $year, int $month): int
    {
        $date = \Carbon\Carbon::create($year, $month, 1);
        return $date->getBusinessDaysInMonth();
    }

    public function toArray(): array
    {
        return [
            'employee' => $this->employee,
            'employer' => $this->employer,
            'year' => $this->year,
            'month' => $this->month,
            'country' => $this->countryCode,
            'business_days' => $this->businessDays,
            'vacation_days' => $this->vacationDays,
            'sick_days' => $this->sickDays,
            'sick_leave_full_pay' => $this->sickLeaveFullPay,
            'years_in_service' => $this->yearsInService,
            'avg_gross_hourly_rate_last_12_months' => $this->avgHourlyRateLast12Months,
            'hours' => [
                'total_hours' => $this->workingHours,
                'working_hours' => $this->workingHours - ( $this->vacationDays * 8) - ($this->sickDays * 8),
                'sick_leave_hours' => $this->sickDays * 8,
                'vacation_hours' => $this->vacationDays * 8,
            ]
        ];
    }
}
