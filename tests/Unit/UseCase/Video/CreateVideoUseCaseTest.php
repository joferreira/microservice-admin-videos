<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Create\CreateVideoUseCase as UseCase;
use Core\UseCase\Video\Create\DTO\CreateInputVideoDTO;
use Core\UseCase\Video\Create\DTO\CreateOutputVideoDTO;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery;
use stdClass;

class CreateVideoUseCaseTest extends TestCase
{   
    protected $useCase;

    protected function setUp(): void
    {
        $this->useCase = new UseCase(
            repository: $this->createMockRepository(),
            transaction: $this->createMockTransaction(),
            storage: $this->createMockFileStorage(),
            eventManager: $this->createMockEventManager(),

            repositoryCategory: $this->createMockRepositoryCategory(),
            repositoryGenre: $this->createMockRepositoryGenre(),
            repositoryCastMember: $this->createMockRepositoryCastMember(),
        );

        parent::setUp();
    }

    public function test_exec_input_output()
    {
        $response = $this->useCase->exec(
            input: $this->createMockInputDTO()
        );

        $this->assertInstanceOf(CreateOutputVideoDTO::class, $response);
    }

    private function createMockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->andReturn($this->createMockEntity());
        return $mockRepository;
    }

    private function createMockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit');
        $mockTransaction->shouldReceive('rollback');
        return $mockTransaction;
    }

    private function createMockFileStorage()
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')->andReturn('path/file.png');
        return $mockFileStorage;
    }

    private function createMockEventManager()
    {   
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch');
        return $mockEventManager;
    }

    private function createMockInputDTO()
    {
        return Mockery::mock(CreateInputVideoDTO::class, [
            'title',
            'description',
            2029,
            12,
            true,
            Rating::RATE12,
            [],
            [],
            []
        ]);
    }

    private function createMockEntity()
    {
        return Mockery::mock(Entity::class, [
            'title',
            'description',
            2029,
            12,
            true,
            Rating::RATE12,
        ]);
    }

    private function createMockRepositoryCategory(array $categoriesResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($categoriesResponse);
        return $mockRepository;
    }

    private function createMockRepositoryGenre(array $genresResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($genresResponse);
        return $mockRepository;
    }

    private function createMockRepositoryCastMember(array $castMembersResponse = [])
    {
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('getIdsListIds')->andReturn($castMembersResponse);
        return $mockRepository;
    }
}
