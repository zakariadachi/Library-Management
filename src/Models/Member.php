<?php

namespace Src\Models;

abstract class Member
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $email,
        protected string $phone,
        protected string $expiryDate,
        protected float $unpaidFees = 0.0
    ) {}

    // Getters
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPhone(): string { return $this->phone; }
    public function getExpiryDate(): string { return $this->expiryDate; }
    public function getUnpaidFees(): float { return $this->unpaidFees; }

    // Logic Methods
    public function addFee(float $amount): void
    {
        $this->unpaidFees += $amount;
    }

    public function payFees(float $amount): void
    {
        $this->unpaidFees = max(0, $this->unpaidFees - $amount);
    }

    public function canBorrow(): bool
    {
        if ($this->unpaidFees > 10.00) {
            return false;
        }
        
        if (strtotime($this->expiryDate) < time()) {
            return false;
        }

        return true;
    }

    // Abstract methods
    abstract public function getMaxBooks(): int;
    abstract public function getLoanPeriod(): int;
    abstract public function getLateFeeRate(): float;
}
