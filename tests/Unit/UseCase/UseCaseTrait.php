<?php

namespace Tests\Unit\UseCase;

use Core\Domain\Repository\PaginationInterface;
use Mockery;
use stdClass;

trait UseCaseTrait
{
    protected function mockPagination(array $items = [])
    {
        $this->mockPaginate = Mockery::mock(stdClass::class, PaginationInterface::class);
        $this->mockPaginate->shouldReceive('items')->andReturn($items);
        $this->mockPaginate->shouldReceive('total')->andReturn(0);
        $this->mockPaginate->shouldReceive('lastPage')->andReturn(0);
        $this->mockPaginate->shouldReceive('firstPage')->andReturn(0);
        $this->mockPaginate->shouldReceive('currentPage')->andReturn(0);
        $this->mockPaginate->shouldReceive('perPage')->andReturn(0);
        $this->mockPaginate->shouldReceive('to')->andReturn(0);
        $this->mockPaginate->shouldReceive('from')->andReturn(0);

        return $this->mockPaginate;
    }
}