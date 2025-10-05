<?php

namespace Dgtlinf\SalaryCalculator\Models;

class EmployeeProfile
{
    public string $firstName;
    public string $lastName;
    public ?string $address;
    public ?string $idNumber;
    public ?string $bankAccount;
    public ?string $position;


    public function __construct(
        string $firstName,
        string $lastName,
        ?string $address = null,
        ?string $idNumber = null,
        ?string $bankAccount = null,
        ?string $position = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->address = $address;
        $this->idNumber = $idNumber;
        $this->bankAccount = $bankAccount;
        $this->position = $position;
    }


    public function fullName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'id_number' => $this->idNumber,
            'bank_account' => $this->bankAccount,
            'position' => $this->position,
        ];
    }
}
