<?php

namespace Dgtlinf\SalaryCalculator\SalaryProviders\RS;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Cmixin\BusinessDay;
use Dgtlinf\SalaryCalculator\Contracts\AverageHourlyRateInterface;

class RSAverageHourlyRate implements AverageHourlyRateInterface
{
    public function __construct()
    {
        BusinessDay::enable(Carbon::class);
        BusinessDay::enable(CarbonImmutable::class);
        Carbon::setHolidaysRegion('rs');
    }

    public function calculate(array $monthlyGrosses): float
    {
        if (empty($monthlyGrosses)) {
            return 0.0;
        }

        $totalGross = 0.0;
        $totalHours = 0;

        foreach ($monthlyGrosses as $entry) {
            $date = $entry['date'] instanceof Carbon
                ? $entry['date']
                : Carbon::parse($entry['date']);

            $gross = (float) $entry['gross'];
            $businessDays = $date->getBusinessDaysInMonth();
            $hours = $businessDays * 8;

            $totalGross += $gross;
            $totalHours += $hours;
        }

        if ($totalHours === 0) {
            return 0.0;
        }

        $monthsCount = count($monthlyGrosses);
        if ($monthsCount < 12) {
            $averageGross = $totalGross / $monthsCount;
            $averageHours = $totalHours / $monthsCount;
            return round($averageGross / $averageHours, 2);
        }

        return round($totalGross / $totalHours, 2);
    }
}
