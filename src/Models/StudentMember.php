<?php

namespace App\Models;

class StudentMember extends Member {
    public function getBorrowLimit(): int {
        return 3;
    }

    public function getLoanPeriodDays(): int {
        return 14;
    }

    public function getLateFeeRatePerDay(): float {
        return 0.50;
    }
}
