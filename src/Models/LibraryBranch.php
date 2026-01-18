<?php

namespace App\Models;

class LibraryBranch {
    private int $id;
    private string $name;
    private string $location;
    private string $openingHours;
    private string $contactInfo;

    public function __construct(string $name,string $location,string $openingHours = '',string $contactInfo = '',?int $id = null) 
    {
        if ($id !== null) {
            $this->id = $id;
        }
        $this->name = $name;
        $this->location = $location;
        $this->openingHours = $openingHours;
        $this->contactInfo = $contactInfo;
    }

    public function getId(): int { 
        return $this->id; 
    }
    public function getName(): string { 
        return $this->name; 
    }
    public function getLocation(): string {
         return $this->location; 
        }
    public function getOpeningHours(): string {
         return $this->openingHours; 
        }
    public function getContactInfo(): string {
         return $this->contactInfo; 
        }
}
