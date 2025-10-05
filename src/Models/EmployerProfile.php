<?php

namespace Dgtlinf\SalaryCalculator\Models;

class EmployerProfile
{
    public string $name;
    public ?string $taxId;
    public ?string $registrationNumber;
    public ?string $address;
    public ?string $bankName;
    public ?string $bankAccount;

    public function __construct(
        string $name,
        ?string $taxId = null,
        ?string $registrationNumber = null,
        ?string $address = null,
        ?string $bankName = null,
        ?string $bankAccount = null
    ) {
        $this->name = $name;
        $this->taxId = $taxId;
        $this->registrationNumber = $registrationNumber;
        $this->address = $address;
        $this->bankName = $bankName;
        $this->bankAccount = $bankAccount;
    }


    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'tax_id' => $this->taxId,
            'registration_number' => $this->registrationNumber,
            'address' => $this->address,
            'bank_name' => $this->bankName,
            'bank_account' => $this->bankAccount,
        ];
    }
}
