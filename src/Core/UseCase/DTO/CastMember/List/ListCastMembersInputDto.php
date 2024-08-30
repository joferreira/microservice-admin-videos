<?php

namespace Core\UseCase\DTO\CastMember\List;

class ListCastMembersInputDto
{
    public function __construct(
        public ?string $filter = null,
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15,
    ) {}
}
