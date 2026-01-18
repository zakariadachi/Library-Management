<?php

namespace App\Models;

class FacultyMember extends Member {
    public function getBorrowLimit(): int {
        return 10;
    }

    public function getLoanPeriodDays(): int {
        return 30;
    }

    public function getLateFeeRatePerDay(): float {
        return 0.25;
    }
}
