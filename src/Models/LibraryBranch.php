<?php

namespace Src\Models;

class LibraryBranch
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $location,
        private string $contactInfo
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getLocation(): string { return $this->location; }
    public function getContactInfo(): string { return $this->contactInfo; }
}
