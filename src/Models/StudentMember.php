<?php

namespace Src\Models;

class StudentMember extends Member
{
    public function getMaxBooks(): int
    {
        return 3;
    }

    public function getLoanPeriod(): int
    {
        return 14;
    }

    public function getLateFeeRate(): float
    {
        return 0.50;
    }
}
