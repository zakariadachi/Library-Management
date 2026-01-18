<?php

namespace Src\Models;

class Reservation
{
    public function __construct(
        private ?int $id,
        private string $memberId,
        private string $bookIsbn,
        private string $reservationDate,
        private string $status = 'Pending' // Pending, Fulfilled, Cancelled
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getMemberId(): string { return $this->memberId; }
    public function getBookIsbn(): string { return $this->bookIsbn; }
    public function getReservationDate(): string { return $this->reservationDate; }
    public function getStatus(): string { return $this->status; }
}
