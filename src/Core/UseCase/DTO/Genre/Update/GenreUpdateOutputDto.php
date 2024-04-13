<?php

namespace Core\UseCase\DTO\Genre\Update;

class GenreUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $isActive = true,
        public string $created_at = ''
    ) {}
}