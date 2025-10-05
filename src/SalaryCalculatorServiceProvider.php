<?php

namespace Dgtlinf\SalaryCalculator;

use Dgtlinf\SalaryCalculator\Services\AverageHourlyRateService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SalaryCalculatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('salary-calculator')
            ->hasConfigFile();
    }

    public function boot()
    {
        parent::boot();

        // Register facade service
        $this->app->singleton('average-hourly-rate', fn() => new AverageHourlyRateService());

        // Register all country providers from config
        $this->app->singleton(SalaryCalculatorManager::class, function () {
            $manager = new SalaryCalculatorManager();

            $providers = config('salary-calculator.providers', []);

            foreach ($providers as $countryCode => $providerClass) {
                $manager->register($countryCode, $providerClass);
            }

            return $manager;
        });
    }
}
