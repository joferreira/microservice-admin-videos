<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre AS EntityGenre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDto;
use Core\UseCase\DTO\Genre\GenreInputDto;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    public function test_delete()
    {
        $uuid = (string) Uuid::uuid4();
        // Arrange
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        // Expect
        $mockRepository->shouldReceive('delete')
                        ->once()
                        ->with($uuid)
                        ->andReturn(true);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [$uuid]);

        $useCase = new DeleteGenreUseCase($mockRepository);
        // Action
        $response = $useCase->execute($mockInputDto);
        // Assert
        $this->assertInstanceOf(DeleteGenreOutputDto::class, $response);
        $this->assertTrue($response->success);

    }

    public function test_delete_fail()
    {
        $uuid = (string) Uuid::uuid4();
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')
                        ->times(1)
                        ->with($uuid)
                        ->andReturn(false);

        $mockInputDto = Mockery::mock(GenreInputDto::class, [$uuid]);

        $useCase = new DeleteGenreUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertFalse($response->success);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

}
