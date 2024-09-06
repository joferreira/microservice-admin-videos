<?php

namespace Core\UseCase\Video\Paginate\DTO;

class PaginateInputVideoDTO
{
    public function __construct(
        public ?string $filter = null,
        public string $order = 'DESC',
        public int $page = 1,
        public int $totalPerPage = 15,
    ) {}
}