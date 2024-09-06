<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Paginate\DTO\PaginateInputVideoDTO;
use Core\UseCase\Video\Paginate\DTO\PaginateOutputVideoDTO;
use Core\UseCase\Video\Paginate\ListVideosUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\UseCase\UseCaseTrait;

class ListVideosUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function test_list_paginate()
    {
        $useCase = new ListVideosUseCase(
            repository: $this->mockRepository()
        );

        $response = $useCase->exec(
            input: $this->mockInputDTO()
        );

        $this->assertInstanceOf(PaginateOutputVideoDTO::class, $response);
        
        $this->assertTrue(true);

    }

    public function mockInputDTO()
    {
        return Mockery::mock(PaginateInputVideoDTO::class, [
            '',
            'DESC',
            1,
            15,
        ]);
    }

    private function mockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')
                        ->once()
                        ->andReturn($this->mockPagination());

        return $mockRepository;
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}