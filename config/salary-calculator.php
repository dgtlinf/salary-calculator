<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Country
    |--------------------------------------------------------------------------
    |
    | Defines the default country ISO code used when no country is explicitly
    | provided in the SalaryContext. This acts as a fallback for all
    | calculations, ensuring consistent behavior across environments.
    |
    */
    'default_country' => 'RS',

    /*
    |--------------------------------------------------------------------------
    | Country Providers
    |--------------------------------------------------------------------------
    |
    | Maps each country ISO code to its corresponding SalaryProvider class.
    | Each provider extends the base SalaryCalculator and implements all
    | country-specific logic for tax tables, contributions, and salary
    | calculation rules.
    |
    | Users can register their own custom providers here to support
    | additional countries or override existing ones.
    |
    */
    'providers' => [
        'RS' => \Dgtlinf\SalaryCalculator\SalaryProviders\RS\RSCountryProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Output Validators
    |--------------------------------------------------------------------------
    |
    | Maps each country ISO code to a Validator class responsible for
    | validating the final salary output structure. Each validator
    | ensures that the calculated salary array conforms to the expected
    | data format and contains all required fields.
    |
    | Country-specific validators may extend the BaseOutputValidator
    | to include additional checks.
    |
    */
    'validators' => [
        'RS' => \Dgtlinf\SalaryCalculator\Validators\RSOutputValidator::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Rounding Precision
    |--------------------------------------------------------------------------
    |
    | Defines the number of decimal places used when rounding numeric values
    | throughout all salary calculations. This is applied in the RoundingTrait
    | and ensures consistent monetary precision across all providers.
    |
    */
    'rounding_precision' => 2,

    /*
    |--------------------------------------------------------------------------
    | Behavior Settings
    |--------------------------------------------------------------------------
    |
    | Controls global runtime behavior for validation, fallbacks, and output:
    |
    | - "strict_validation": if TRUE, throws an exception when a validator
    |   for the given country is not found.
    | - "fallback_to_base_validator": if TRUE, uses the BaseOutputValidator
    |   when no country-specific validator exists.
    | - "include_context_in_output": if FALSE, omits the context block from
    |   the final salary output array.
    |
    */
    'behavior' => [
        'strict_validation' => false,
        'fallback_to_base_validator' => true,
        'include_context_in_output' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Tax Tables Path
    |--------------------------------------------------------------------------
    |
    | Optional path for loading external tax tables outside the package.
    | This allows projects to override or extend built-in tax tables
    | (e.g. for different years or custom rates) without modifying the
    | package source.
    |
    | Example:
    |   database/salary_tax_tables/RS/2026.php
    |
    */
    'tax_tables_path' => base_path('database/salary_tax_tables'),

];
