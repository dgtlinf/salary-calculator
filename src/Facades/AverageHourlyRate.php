<?php

namespace Dgtlinf\SalaryCalculator\Facades;


use Illuminate\Support\Facades\Facade;

class AverageHourlyRate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'average-hourly-rate';
    }
}
