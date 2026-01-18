<?php

namespace Src\Models;

class Author
{
    public function __construct(
        private ?int $id,
        private string $name,
        private ?string $biography = null,
        private ?string $nationality = null,
        private ?string $birthDate = null
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getBiography(): ?string { return $this->biography; }
    public function getNationality(): ?string { return $this->nationality; }
    public function getBirthDate(): ?string { return $this->birthDate; }
}
