<?php

namespace Dgtlinf\SalaryCalculator\Facades;

use Illuminate\Support\Facades\Facade;

class SalaryCalculator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dgtlinf\SalaryCalculator\SalaryCalculatorManager::class;
    }
}
