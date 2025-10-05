# Salary Calculator for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dgtlinf/salary-calculator.svg?style=flat-square)](https://packagist.org/packages/dgtlinf/salary-calculator)
[![GitHub Tests Action Status](https://github.com/dgtlinf/salary-calculator/actions/workflows/run-tests.yml/badge.svg)](https://github.com/dgtlinf/salary-calculator/actions/workflows/run-tests.yml)
[![License](https://img.shields.io/github/license/dgtlinf/salary-calculator.svg?style=flat-square)](https://github.com/dgtlinf/salary-calculator/blob/main/LICENSE.md)

A modern, extensible Laravel package for standardized salary calculations across multiple countries.  
Supports detailed breakdowns for gross, net, contributions, and total employer costs — starting with Serbia (RS).

---

## 📦 Installation

Install the package via Composer:

```bash
composer require dgtlinf/salary-calculator
```

---

## ⚙️ Usage Example

```php
use Dgtlinf\SalaryCalculator\Facades\SalaryCalculator;
use Dgtlinf\SalaryCalculator\Models\{
    SalaryContext,
    EmployeeProfile,
    EmployerProfile
};

$employee = new EmployeeProfile(
    firstName: 'Milan',
    lastName: 'Jovanović',
    address: 'Kralja Petra 10, Beograd',
    idNumber: '0101990123456',
    bankAccount: '160-123456789-01',
    position: 'Software Engineer'
);

$employer = new EmployerProfile(
    name: 'Digital Infinity DOO',
    taxId: '110217311',
    registrationNumber: '21318507',
    address: 'Bulevar Kralja Petra I 89, Novi Sad',
    bankName: 'Raiffeisen Bank',
    bankAccount: '265-0001234567890-00'
);

$context = new SalaryContext(
    2025,
    9,
    'RS',
    vacationDays: 0,
    sickDays: 0,
    sickLeaveFullPay: false,
    yearsInService: 2,
    avgHourlyRateLast12Months: null,
    employee: $employee,
    employer: $employer
);

// Create the calculator
$calc = SalaryCalculator::for($context);

// Run calculation from gross
$result = $calc->fromGross(790729.64);

// Optionally validate output structure before use
$calc->validateOutput($result);

return response()->json($result);
```

---

## 📊 Example JSON Response

```json
{
  "salary": {
    "gross": {
      "items": {
        "regular_work": { "units": 176, "unit": "_hours", "per_unit": 4441.04, "basis": 1, "amount": 781623.59 },
        "seniority_allowance": { "units": 176, "unit": "_hours", "per_unit": 4441.04, "basis": 0.008, "amount": 6252.99 },
        "meal_allowance": { "units": 22, "unit": "_days", "per_unit": 64.84, "basis": 1, "amount": 1426.53 }
      },
      "total": 790729.64
    },
    "contributions_base": 656425,
    "income_tax": { "base": 762306.64, "amount": 76230.66 },
    "employee_contributions": { "total": 130628.58 },
    "net_salary": 583870.4,
    "employer_contributions": { "total": 99448.39 },
    "total_salary_cost": 890178.03
  },
  "context": { "employee": { "firstName": "Milan" }, "employer": { "name": "Digital Infinity DOO" }, "country": "RS" },
  "tax_table": { "year": 2025, "tax_rate": 0.1, "currency_code": "RSD" }
}
```

---

## ⚙️ Configuration

The package publishes a config file `config/salary-calculator.php` allowing full control over:
- Country providers and validators
- Default rounding precision
- Validation behavior and output structure
- Optional external tax table directory

```bash
php artisan vendor:publish --tag="salary-calculator-config"
```

---

## 🧩 Validation Behavior

`SalaryCalculator::validateOutput($result)` uses the config-defined validators to ensure data structure integrity.

Behavior can be customized in `config/salary-calculator.php`:

| Option | Description |
|--------|--------------|
| `strict_validation` | Throw exception if validator missing |
| `fallback_to_base_validator` | Use BaseOutputValidator if no country-specific validator |
| `include_context_in_output` | Whether to include the `context` block in output |

---

## 🧪 Testing

```bash
composer test
```

---

## 🧱 Architecture Overview

- **SalaryCalculatorManager** — Resolves and loads the correct provider based on country and year.
- **Country Providers** — Contain tax rules and salary logic for each country (e.g., `RSCountryProvider`).
- **Output Validators** — Verify structure of salary data before use (Payslip integration-ready).
- **Tax Tables** — Organized per country and year under `/SalaryProviders/{Country}/TaxTables/{Year}.php`.
- **Traits** — Shared helpers such as `RoundingTrait` for precision and numeric consistency.

---

## 🧾 Changelog

All notable changes are automatically documented in [CHANGELOG.md](CHANGELOG.md).  
Changelog updates automatically on each GitHub release.

---

## 🪪 License

The MIT License (MIT).  
See [LICENSE.md](LICENSE.md) for more information.

---

## 👤 Author

**Digital Infinity DOO**  
Bulevar Kralja Petra I 89, Novi Sad, Serbia  
[www.digitalinfinity.rs](https://www.digitalinfinity.rs)
