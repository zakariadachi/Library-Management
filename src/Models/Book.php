<?php

namespace Src\Models;

class Book
{
    public function __construct(
        private string $isbn,
        private string $title,
        private int $publicationYear,
        private int $categoryId,
        private string $status = 'Available'
    ) {}

    public function getIsbn(): string { return $this->isbn; }
    public function getTitle(): string { return $this->title; }
    public function getPublicationYear(): int { return $this->publicationYear; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getStatus(): string { return $this->status; }

    public function setStatus(string $status): void
    {
        $validStatuses = ['Available', 'Checked Out', 'Reserved'];
        if (in_array($status, $validStatuses)) {
            $this->status = $status;
        }
    }

    public function isAvailable(): bool
    {
        return $this->status === 'Available';
    }
}
