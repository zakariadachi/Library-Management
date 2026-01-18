<?php

namespace Src\Models;

class BorrowRecord
{
    public function __construct(
        private ?int $id,
        private string $memberId,
        private string $bookIsbn,
        private int $branchId,
        private string $borrowDate,
        private string $dueDate,
        private ?string $returnDate = null,
        private float $lateFee = 0.00
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getMemberId(): string { return $this->memberId; }
    public function getBookIsbn(): string { return $this->bookIsbn; }
    public function getBranchId(): int { return $this->branchId; }
    public function getBorrowDate(): string { return $this->borrowDate; }
    public function getDueDate(): string { return $this->dueDate; }
    public function getReturnDate(): ?string { return $this->returnDate; }
    public function getLateFee(): float { return $this->lateFee; }

    public function setReturnDate(string $returnDate): void
    {
        $this->returnDate = $returnDate;
    }

    public function setLateFee(float $fee): void
    {
        $this->lateFee = $fee;
    }
    
    public function isOverdue(): bool
    {
        if ($this->returnDate) {
            return false;
        }
        return strtotime($this->dueDate) < time();
    }
}
