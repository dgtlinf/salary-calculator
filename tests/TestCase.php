<?php

namespace Dgtlinf\SalaryCalculator\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Dgtlinf\SalaryCalculator\SalaryCalculatorServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Register ServiceProvider.
     */
    protected function getPackageProviders($app)
    {
        return [
            SalaryCalculatorServiceProvider::class,
        ];
    }

    /**
     * Facades (AverageHourlyRate, SalaryCalculator...),
     * Mapper
     */
    protected function getPackageAliases($app)
    {
        return [
            'SalaryCalculator' => \Dgtlinf\SalaryCalculator\Facades\SalaryCalculator::class,
            'AverageHourlyRate' => \Dgtlinf\SalaryCalculator\Facades\AverageHourlyRate::class,
        ];
    }
}
