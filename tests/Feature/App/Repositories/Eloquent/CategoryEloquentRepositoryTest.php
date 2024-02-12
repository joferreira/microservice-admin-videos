<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Exception\NotFoundException;
use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new Model());
    }
    public function testInsert()
    {
        $entity = new EntityCategory(
            id: '',
            name: 'Teste'
        );

        $response = $this->repository->insert($entity);

        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertDatabaseHas('categories', [
            'name'=> $entity->name
        ]);
    }

    public function testFindById()
    {
        $category = Model::factory()->create();

        $response = $this->repository->findById($category->id);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
    }

    public function testFindByIdNotFound()
    {
        try {
            $response =$this->repository->findById('idfake');
        } catch (Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testFindAll()
    {
        $categories = Model::factory()->count(10)->create();
        
        $response = $this->repository->findAll();

        $this->assertEquals(count($categories), count($response));
    }

    public function testPaginate()
    {
        Model::factory()->count(20)->create();
        
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateWithout()
    {
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(0, $response->items());
    }

    public function testUpdateNotFound()
    {
        try {
            $category = new EntityCategory(id: '', name: 'test');
            $this->repository->update($category);
        } catch (Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $categoryDb = Model::factory()->create();
        $category = new EntityCategory(
            id: $categoryDb->id,
            name: 'update name',
        );
        $response = $this->repository->update($category);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertNotEquals($response->name, $categoryDb->name);
        $this->assertEquals('update name', $response->name);
    }

    public function testDeleteNotFound()
    {
        try {
            $this->repository->delete('fake_id');

            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }
    
    public function testDelete()
    {
        $categoryDb = Model::factory()->create();
        $response = $this->repository->delete($categoryDb->id);

        $this->assertTrue($response);
    }

}
