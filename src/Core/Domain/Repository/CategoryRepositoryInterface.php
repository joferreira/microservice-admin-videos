<?php

namespace Core\Domain\Repository;

interface CategoryRepositoryInterface extends EntityRepositoryInterface
{
    public function getIdsListIds(array $categoriesId = []): array; 
}