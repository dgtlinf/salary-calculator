<?php

namespace Dgtlinf\SalaryCalculator\Services;

use InvalidArgumentException;
use Dgtlinf\SalaryCalculator\Contracts\AverageHourlyRateInterface;

class AverageHourlyRateService
{
    protected array $providers = [];

    public function __construct()
    {
        $this->providers = [
            'RS' => \Dgtlinf\SalaryCalculator\SalaryProviders\RS\RSAverageHourlyRate::class,
        ];
    }

    public function calculate(array $monthlyGrosses, string $countryCode = 'RS'): float
    {
        $countryCode = strtoupper($countryCode);

        if (!isset($this->providers[$countryCode])) {
            throw new InvalidArgumentException("No AverageHourlyRate provider registered for {$countryCode}");
        }

        /** @var AverageHourlyRateInterface $provider */
        $provider = app($this->providers[$countryCode]);

        return $provider->calculate($monthlyGrosses);
    }
}
