<?php

namespace Core\Domain\Entity;

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class CastMember extends Entity
{
    public function __construct(
        protected string $name,
        protected CastMemberType $type,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    protected function validate()
    {
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name);
    }

    public function update(string $name)
    {
        $this->name = $name;

        $this->validate();
    }
}