<?php

namespace Tests\Unit\UseCase\category;

use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;
use Ramsey\Uuid\Uuid;
use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CategoryOutputDto;

class ListCategoriesUseCaseUnitTest extends TestCase
{
    public function testListCategoriesEmpty()
    {
        $mockPaginate = $this->mockPagination();
        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('paginate')->andReturn($this->mockPaginate);

        $this->mockInputDto = Mockery::mock(stdClass::class, ListCategoriesInputDto::class, ['filter', 'order']);
        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertCount(0, $responseUseCase->items);
        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);

        /**
         * Spies
         */
        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive('paginate')->andReturn($this->mockPaginate);
        $useCase = new ListCategoriesUseCase($this->spy);
        $responseUseCase = $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived('paginate');
    }

    public function testListCategories()
    {
        $register = new stdClass();
        $register->id = 'sts';
        $register->name = 'name';
        $register->description = 'description';
        $register->is_active = 'is_active';
        $register->created_at = 'created_at';
        $register->updated_at = 'updated_at';
        $register->deleted_at = 'deleted_act';
        $mockPaginate = $this->mockPagination([
            $register,
        ]);
        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('paginate')->andReturn($this->mockPaginate);

        $this->mockInputDto = Mockery::mock(stdClass::class, ListCategoriesInputDto::class, ['filter', 'order']);
        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($this->mockInputDto);

        $this->assertCount(1, $responseUseCase->items);
        $this->assertInstanceOf(stdClass::class, $responseUseCase->items[0]);
        $this->assertInstanceOf(ListCategoriesOutputDto::class, $responseUseCase);

    }

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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}