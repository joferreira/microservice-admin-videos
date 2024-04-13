<?php

namespace Tests\Unit\UseCase\Genre;

use Core\UseCase\DTO\Genre\Update\{
    GenreUpdateInputDto,
    GenreUpdateOutputDto
};
use Core\Domain\Entity\Genre As EntityGenre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{
    GenreRepositoryInterface,
    CategoryRepositoryInterface
};
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Core\UseCase\Interfaces\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    public function test_update()
    {
        $uuid = (string) Uuid::uuid4();

        $useCase = new UpdateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        $response = $useCase->execute($this->mockUpdateInputDto($uuid, [$uuid]));

        $this->assertInstanceOf(GenreUpdateOutputDto::class, $response);
    }

    public function test_update_categories_not_found()
    {
        $this->expectException(NotFoundException::class);

        $uuid = (string) Uuid::uuid4();

        $useCase = new UpdateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        $useCase->execute($this->mockUpdateInputDto($uuid, [$uuid, 'fake_id', 'fake_idis']));
    }

    private function mockEntity(string $uuid)
    {
        $mockEntity = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('addCategory');

        return $mockEntity;
    }

    private function mockRepository(string $uuid)
    {
        $mockEntity = $this->mockEntity($uuid);
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->andReturn($mockEntity);

        return $mockRepository;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');

        return $mockTransaction;
    }

    private function mockCategoryRepository(string $uuid)
    {
        $mockCategoryRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepository->shouldReceive('getIdsListIds')->andReturn([$uuid]);

        return $mockCategoryRepository;
    }

    private function mockUpdateInputDto(string $uuid, array $categoriesIds)
    {
        $mockInputCreateDto = Mockery::mock(GenreUpdateInputDto::class, [
            $uuid,'name to update', $categoriesIds, true
        ]);

        return $mockInputCreateDto;
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
