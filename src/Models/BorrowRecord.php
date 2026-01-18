<?php

namespace App\Models;

use DateTime;

class BorrowRecord {
    private ?int $id;
    private Member $member;
    private Book $book;
    private LibraryBranch $branch;
    private DateTime $borrowDate;
    private DateTime $dueDate;
    private ?DateTime $returnDate;
    private float $lateFee;

    public function __construct(Member $member,Book $book,LibraryBranch $branch,DateTime $borrowDate,DateTime $dueDate,?DateTime $returnDate = null,float $lateFee = 0.0,?int $id = null) 
    {
        $this->id = $id;
        $this->member = $member;
        $this->book = $book;
        $this->branch = $branch;
        $this->borrowDate = $borrowDate;
        $this->dueDate = $dueDate;
        $this->returnDate = $returnDate;
        $this->lateFee = $lateFee;
    }

    public function getId(): ?int {
        return $this->id; 
    }
    public function getMember(): Member { 
        return $this->member; 
    }
    public function getBook(): Book { 
        return $this->book; 
    }
    public function getBranch(): LibraryBranch { 
        return $this->branch; 
    }
    public function getBorrowDate(): DateTime { 
        return $this->borrowDate; 
    }
    public function getDueDate(): DateTime {
        return $this->dueDate; 
    }
    public function getReturnDate(): ?DateTime { 
        return $this->returnDate; 
    }
    public function getLateFee(): float { 
        return $this->lateFee; 
    }

    public function setReturnDate(DateTime $date): void { 
        $this->returnDate = $date; 
    }
    public function setLateFee(float $fee): void { 
        $this->lateFee = $fee; 
    }

    // public function calculateLateFee(): float {
    //     if ($this->returnDate === null) {
    //         $now = new DateTime();
    //         if ($now <= $this->dueDate) {
    //             return 0.0;
    //         }
    //         $diff = $now->diff($this->dueDate);
    //         $days = $diff->days;
    //         return $days * $this->member->getLateFeeRatePerDay();
    //     }

    //     if ($this->returnDate <= $this->dueDate) {
    //         return 0.0;
    //     }

    //     $diff = $this->returnDate->diff($this->dueDate);
    //     $days = $diff->days;
    //     return $days * $this->member->getLateFeeRatePerDay();
    // }
}
