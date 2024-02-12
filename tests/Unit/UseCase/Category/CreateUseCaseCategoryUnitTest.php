<?php

namespace Tests\Unit\UseCase\category;

use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Core\UseCase\DTO\Category\CreateCategory\{
    CategoryCreateInputDto,
    CategoryCreateOutputDto
};

class CreateUseCaseCategoryUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {   
        $uuid = Uuid::uuid4()->toString();
        $categoryName = 'name cat';

        $this->mockEntity = Mockery::mock(Category::class, [
            $uuid,
            $categoryName,
        ]);

        $this->mockEntity->shouldReceive('id')->andReturn($uuid);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));

        // $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        // $this->mockRepo->shouldReceive('insert')->andReturn($this->mockEntity);
        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('insert')
            ->times(1)
            ->andReturn($this->mockEntity);

        $this->mockInputDto = Mockery::mock(CategoryCreateInputDto::class, [
            $categoryName,
        ]);
        $usecase = new CreateCategoryUseCase($this->mockRepo);
        $responseUseCase = $usecase->execute($this->mockInputDto);

        $this->assertInstanceOf(CategoryCreateOutputDto::class, $responseUseCase);
        $this->assertEquals($categoryName, $responseUseCase->name);
        $this->assertEquals('', $responseUseCase->description);

        /**
         * Spies
         */
        // $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        // $this->spy->shouldReceive('insert')->andReturn($this->mockEntity);
        // $usecase = new CreateCategoryUseCase($this->spy);
        // $responseUseCase = $usecase->execute($this->mockInputDto);
        // $this->spy->shouldHaveReceived('insert');

        Mockery::close();
    }
}