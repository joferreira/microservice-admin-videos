<?php

namespace Tests\Unit\UseCase\Genre;

use Core\UseCase\DTO\Genre\Create\{
    GenreCreateInputDto,
    GenreCreateOutputDto
};
use Core\Domain\Entity\Genre As EntityGenre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{
    GenreRepositoryInterface,
    CategoryRepositoryInterface
};
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interfaces\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{
    public function test_create()
    {
        $uuid = (string) Uuid::uuid4();

        $useCase = new CreateGenreUseCase($this->mockRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        $response = $useCase->execute($this->mockCreateInputDto([$uuid]));

        $this->assertInstanceOf(GenreCreateOutputDto::class, $response);
    }

    public function test_create_categories_not_found()
    {
        $this->expectException(NotFoundException::class);

        $uuid = (string) Uuid::uuid4();

        $useCase = new CreateGenreUseCase($this->mockRepository($uuid, 0), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        $useCase->execute($this->mockCreateInputDto([$uuid, 'fake_id', 'fake_idis']));
    }

    private function mockEntity(string $uuid)
    {
        $mockEntity = Mockery::mock(EntityGenre::class, [
            'teste', new ValueObjectUuid($uuid), true, []
        ]);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        return $mockEntity;
    }

    private function mockRepository(string $uuid, int $timesCalled = 1)
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')
                        ->times($timesCalled)
                        ->andReturn($this->mockEntity($uuid));

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
        $mockCategoryRepository->shouldReceive('getIdsListIds')->once()->andReturn([$uuid]);

        return $mockCategoryRepository;
    }

    private function mockCreateInputDto(array $categoriesIds)
    {
        $mockInputCreateDto = Mockery::mock(GenreCreateInputDto::class, [
            'name', $categoriesIds, true
        ]);

        return $mockInputCreateDto;
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
