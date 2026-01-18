<?php

namespace App\Models;

use DateTime;

abstract class Member {
    protected int $id;
    protected string $fullName;
    protected string $email;
    protected string $phone;
    protected DateTime $membershipExpiry;
    protected float $unpaidFees;

    public function __construct(string $fullName,string $email,string $phone,DateTime $membershipExpiry,float $unpaidFees = 0.0,?int $id = null) 
    {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->fullName = $fullName;
        $this->email = $email;
        $this->phone = $phone;
        $this->membershipExpiry = $membershipExpiry;
        $this->unpaidFees = $unpaidFees;
    }

    // Getters and Setters
    public function getId(): int { 
        return $this->id; 
    }
    public function getFullName(): string { 
        return $this->fullName; 
    }
    public function getEmail(): string { 
        return $this->email; 
    }
    public function getPhone(): string { 
        return $this->phone; 
    }
    public function getMembershipExpiry(): DateTime { 
        return $this->membershipExpiry; 
    }
    public function getUnpaidFees(): float { 
        return $this->unpaidFees; 
    }

    public function setFullName(string $name): void { 
        $this->fullName = $name; 
    }
    public function setEmail(string $email): void { 
        $this->email = $email; 
    }
    public function setPhone(string $phone): void { 
        $this->phone = $phone; 
    }
    public function setMembershipExpiry(DateTime $expiry): void { 
        $this->membershipExpiry = $expiry; 
    }
    // public function setUnpaidFees(float $fees): void { 7
    //     $this->unpaidFees = $fees; 
    // }

    // public function isMembershipExpired(): bool {
    //     return new DateTime() > $this->membershipExpiry;
    // }

    // public function canBorrow(): bool {
    //     if ($this->isMembershipExpired()) {
    //         return false;
    //     }
    //     if ($this->unpaidFees > 10.0) {
    //         return false;
    //     }
    //     return true;
    // }

    // abstract public function getBorrowLimit(): int;
    // abstract public function getLoanPeriodDays(): int;
    // abstract public function getLateFeeRatePerDay(): float;
}
