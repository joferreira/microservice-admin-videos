<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\List\DTO\ListInputVideoDTO;
use Core\UseCase\Video\List\DTO\ListOutputVideoDTO;
use Core\UseCase\Video\List\ListVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListVideoUseCaseUnitTest extends TestCase
{
    public function test_list()
    {
        $uuid = (string) Uuid::random();
        $useCase = new ListVideoUseCase(
            repository: $this->mockRepository()
        );

        $response = $useCase->exec(
            input: $this->mockInputDTO($uuid)
        );

        $this->assertInstanceOf(ListOutputVideoDTO::class, $response);

        $this->assertTrue(true);
    }

    private function mockRepository()
    {
        $mockRepository = Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')
                        ->once()
                        ->andReturn($this->getEntity());
        
            return $mockRepository;
    }

    private function getEntity(): Entity
    {
        return new Entity(
            title: 'title',
            description: 'description',
            yearLaunched: 2021,
            duration: 120,
            opened: true,
            rating: Rating::L
        );
    }

    public function mockInputDTO(string $id)
    {
        return Mockery::mock(ListInputVideoDTO::class, [$id]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}