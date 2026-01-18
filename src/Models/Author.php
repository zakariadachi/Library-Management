<?php

namespace App\Models;

class Author {
    private int $id;
    private string $name;
    private string $biography;
    private string $nationality;
    private ?string $birthDate;
    private ?string $deathDate;
    private string $primaryGenre;

    public function __construct(string $name,string $biography = '',string $nationality = '',?string $birthDate = null,?string $deathDate = null,string $primaryGenre = '',?int $id = null) 
    {
        
        $this->id = $id;
        $this->name = $name;
        $this->biography = $biography;
        $this->nationality = $nationality;
        $this->birthDate = $birthDate;
        $this->deathDate = $deathDate;
        $this->primaryGenre = $primaryGenre;
    }

    public function getId(): int {
        return $this->id; 
    }
    public function getName(): string {
        return $this->name;
    }
    public function getBiography(): string { 
        return $this->biography;
    }
    public function getNationality(): string {
        return $this->nationality;
    }
    public function getBirthDate(): ?string {
        return $this->birthDate; 
    }
    public function getDeathDate(): ?string {
        return $this->deathDate; 
    }
    public function getPrimaryGenre(): string {
        return $this->primaryGenre; 
    }
}
