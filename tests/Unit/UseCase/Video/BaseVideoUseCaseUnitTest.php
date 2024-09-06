<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use PHPUnit\Framework\TestCase;
use Mockery;
use stdClass;

abstract class BaseVideoUseCaseUnitTest extends TestCase
{   
    protected $useCase;

    abstract protected function nameActionRepository(): string;
    abstract protected function getUseCase(): string;
    abstract protected function createMockInputDTO(
        array $categoriesIds = [],
        array $genresIds = [],
        array $castMembersIds = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    );

    protected function createUseCase(
        int $timesCallMethodActionRepository = 1,
        int $timesCallUpdateMediaRepository = 1,

        int $timesCallMethodCommitTransaction = 1,
        int $timesCallMethodRollbackTransaction = 0,

        int $timesCallMethodFileStorage = 0,

        int $timesCallMethodDispatchEventManager = 0
    )
    {
        $this->useCase = new ($this->getUseCase())(
            repository: $this->createMockRepository(
                timesCallAction: $timesCallMethodActionRepository,
                timesCallUpdateMedia: $timesCallUpdateMediaRepository
            ),
            transaction: $this->createMockTransaction(
                timesCallCommit: $timesCallMethodCommitTransaction,
                timesCallRollback: $timesCallMethodRollbackTransaction
            ),
            storage: $this->createMockFileStorage(
                timesCallStore: $timesCallMethodFileStorage
            ),
            eventManager: $this->createMockEventManager(
                times: $timesCallMethodDispatchEventManager
            ),

            repositoryCategory: $this->createMockRepositoryCategory(),
            repositoryGenre: $this->createMockRepositoryGenre(),
            repositoryCastMember: $this->createMockRepositoryCastMember(),
        );
    }

    /**
     * @dataProvider dataProvidersIds
     */
    public function test_exception_categories_ids(
        string $label,
        array $ids
    )
    {
        $this->createUseCase(
            timesCallMethodActionRepository: 0,
            timesCallUpdateMediaRepository: 0,
            timesCallMethodCommitTransaction: 0,
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            '%s %s not found',
            $label,
            implode(', ', $ids)
        ));
        $this->useCase->exec(
            input: $this->createMockInputDTO(
                categoriesIds: $ids
            )
        );
    }

    public function dataProvidersIds(): array
    {
        return [
            ['Category', ['uuid_1']],
            ['Categories', ['uuid_1', 'uuid_2']],
            ['Categories', ['uuid_1', 'uuid_2', 'uuid_3', 'uuid_4']],
        ];
    }

    /**
     * @dataProvider dataProvidersFiles
     */
    public function test_upload_files(
        array $video,
        array $trailer,
        array $thumb,
        array $thumbHalf,
        array $banner,
        int $storage,
        int $event = 0,
    )
    {
        $this->createUseCase(
            timesCallMethodFileStorage: $storage,
            timesCallMethodDispatchEventManager: $event
        );

        $response = $this->useCase->exec(
            input: $this->createMockInputDTO(
                videoFile: $video['value'],
                trailerFile: $trailer['value'],
                thumbFile: $thumb['value'],
                thumbHalf: $thumbHalf['value'],
                bannerFile: $banner['value'],
            )
        );

        $this->assertEquals($response->videoFile, $video['expected']);
        $this->assertEquals($response->trailerFile, $trailer['expected']);
        $this->assertEquals($response->thumbFile, $thumb['expected']);
        $this->assertEquals($response->thumbHalf, $thumbHalf['expected']);
        $this->assertEquals($response->bannerFile, $banner['expected']);
    }

    public function dataProvidersFiles(): array
    {
        return [
            [
                'video' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'trailer' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'thumb' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 5,
                'dispatch' => 1
            ],
            [
                'video' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 3,
                'dispatch' => 1
            ],
            [
                'video' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => ['tmp' => 'tmp/file.png'], 'expected' => 'path/file.png'],
                'timesStorage' => 2
            ],
            [
                'video' => ['value' => null, 'expected' => null],
                'trailer' => ['value' => null, 'expected' => null],
                'thumb' => ['value' => null, 'expected' => null],
                'thumbHalf' => ['value' => null, 'expected' => null],
                'banner' => ['value' => null, 'expected' => null],
                'timesStorage' => 0
            ]
        ];
    }

    private function createMockRepository(
        int $timesCallAction,
        int $timesCallUpdateMedia
    )
    {
        $entity = $this->createEntity();
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive($this->nameActionRepository())
                        ->times($timesCallAction)
                        ->andReturn($entity);
        $mockRepository->shouldReceive('findById')
                        ->andReturn($entity);
        $mockRepository->shouldReceive('updateMedia')
                        ->times($timesCallUpdateMedia);
        return $mockRepository;
    }

    private function createMockTransaction(
        int $timesCallCommit,
        int $timesCallRollback
    )
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive('commit')
                        ->times($timesCallCommit);
        $mockTransaction->shouldReceive('rollback')
                        ->times($timesCallRollback);
        return $mockTransaction;
    }

    private function createMockFileStorage(int $timesCallStore)
    {
        $mockFileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $mockFileStorage->shouldReceive('store')
                        ->times($timesCallStore)
                        ->andReturn('path/file.png');
        return $mockFileStorage;
    }

    private function createMockEventManager(int $times)
    {   
        $mockEventManager = Mockery::mock(stdClass::class, VideoEventManagerInterface::class);
        $mockEventManager->shouldReceive('dispatch')
                        ->times($times);
        return $mockEventManager;
    }

    private function createEntity()
    {
        return new Entity(
            title: 'title',
            description: 'description',
            yearLaunched: 2021,
            duration: 90,
            opened: true,
            rating: Rating::RATE10
        );
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
