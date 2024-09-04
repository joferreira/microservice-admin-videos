<?php

namespace Core\Domain\Entity;

use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre extends Entity
{
    public function __construct(
        protected string $name,
        protected ?Uuid $id = null,
        protected $isActive = true,
        protected array $categoriesId = [],
        protected ?DateTime $createdAt = null
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function update(string $name)
    {
        $this->name = $name;

        $this->validate();
    }

    public function addCategory(string $categoryId)
    {
        array_push($this->categoriesId, $categoryId);
    }

    public function removeCategory(string $categoryId)
    {
        unset($this->categoriesId[array_search($categoryId, $this->categoriesId)]);
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
    }
    
}