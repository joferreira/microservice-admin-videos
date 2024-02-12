<?php

namespace Tests\Unit\UseCase\category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\DeleteCategory\CategoryDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testeDelete()
    {   
        $uuid = Uuid::uuid4()->toString();

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('delete')->andReturn(true);

        $this->mockInput = Mockery::mock(CategoryInputDto::class, [
            $uuid,
        ]);

        $useCase = new DeleteCategoryUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInput);

        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        /**
         * Spies
         */
        $this->spy = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive('delete')->andReturn(true);

        $useCase = new DeleteCategoryUseCase($this->spy);
        $responseUseCase = $useCase->execute($this->mockInput);

        $this->spy->shouldHaveReceived('delete');

    }

    public function testeDeleteFalse()
    {   
        $uuid = Uuid::uuid4()->toString();

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('delete')->andReturn(false);

        $this->mockInput = Mockery::mock(CategoryInputDto::class, [
            $uuid,
        ]);

        $useCase = new DeleteCategoryUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInput);

        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);

        /**
         * Spies
         */
        $this->spy = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive('delete')->andReturn(false);

        $useCase = new DeleteCategoryUseCase($this->spy);
        $responseUseCase = $useCase->execute($this->mockInput);

        $this->spy->shouldHaveReceived('delete');

    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}