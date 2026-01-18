<?php

namespace App\Models;

class Book {
    private string $isbn;
    private string $title;
    private int $publicationYear;
    private string $category;
    private string $status;
    private array $authors = [];
    private int $totalCopies;
    private int $availableCopies;

    public function __construct(string $isbn,string $title,int $publicationYear,string $category,int $totalCopies = 1,int $availableCopies = 1,string $status = 'Available')
    {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->publicationYear = $publicationYear;
        $this->category = $category;
        $this->totalCopies = $totalCopies;
        $this->availableCopies = $availableCopies;
        $this->status = $status;
    }

    public function getIsbn(): string {
        return $this->isbn; 
    }
    public function getTitle(): string {
        return $this->title; 
    }
    public function getPublicationYear(): int { 
        return $this->publicationYear; 
    }
    public function getCategory(): string { 
        return $this->category; 
    }
    public function getStatus(): string { 
        return $this->status; 
    }
    public function getAuthors(): array { 
        return $this->authors; 
    }
    public function getTotalCopies(): int { 
        return $this->totalCopies; 
    }
    public function getAvailableCopies(): int { 
        return $this->availableCopies; 
    }

    public function setStatus(string $status): void {
        $this->status = $status; 
        }
    public function addAuthor(Author $author): void {
        $this->authors[] = $author; 
        }
    public function setAvailableCopies(int $count): void { 
        $this->availableCopies = $count; 
    }

    // public function isAvailable(): bool {
    //     return $this->availableCopies > 0 && $this->status === 'Available';
    // }
}
