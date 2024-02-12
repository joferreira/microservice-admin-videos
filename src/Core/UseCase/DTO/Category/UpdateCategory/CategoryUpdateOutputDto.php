<?php

namespace Core\UseCase\DTO\Category\UpdateCategory;

class CategoryUpdateOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string|null $description = null,
        public bool $is_active = true,
        public string $created_at = '',
    ) {}
}