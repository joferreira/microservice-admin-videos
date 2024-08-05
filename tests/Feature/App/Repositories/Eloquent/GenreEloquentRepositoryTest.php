<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category;
use App\Models\Genre as Model;
use Core\Domain\Entity\Genre as EntityGenre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Uuid;
use Database\Factories\CategoryFactory;
use DateTime;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Spatie\FlareClient\Http\Exceptions\NotFound;
use Tests\TestCase;

class GenreEloquentRepositoryTest extends TestCase
{
    protected $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new Model());
    }

    public function testImplementsInstance()
    {
        $this->assertInstanceOf(GenreEloquentRepository::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new EntityGenre(name: 'New Genre');

        $response = $this->repository->insert($entity);

        $this->assertEquals($entity->name, $response->name);
        $this->assertEquals($entity->id, $response->id);

        $this->assertDatabaseHas('genres', [
            'id'=> $entity->id()
        ]);

    }

    public function testInsertDeactivate()
    {
        $entity = new EntityGenre(name: 'New Genre 2');
        $entity->deactivate();

        $this->repository->insert($entity);

        $this->assertDatabaseHas('genres', [
            'id' => $entity->id(),
            'is_active' => false
        ]);
    }

    public function testInsertWithRelationships()
    {
        $categories = Category::factory()->count(4)->create();

        $genre = new EntityGenre(name: 'New Genre 3');
        foreach ($categories as $category) {
            $genre->addCategory($category->id);
        }

        $response = $this->repository->insert($genre);

        $this->assertDatabaseHas('genres', [
            'id' => $response->id(),
        ]);

        $this->assertDatabaseCount('category_genre', 4);
    }

    public function testNotFoundById()
    {
        $this->expectException(NotFoundException::class);
        $genre = 'Genre not found';

        $this->repository->findById($genre);
    }

    public function testFindById()
    {
        $genre = Model::factory()->create();

        $response = $this->repository->findById($genre->id);

        $this->assertEquals($genre->id, $response->id());
        $this->assertEquals($genre->name, $response->name);
    }

    public function testFindAll()
    {
        $genres = Model::factory()->count(10)->create();

        $genresDb = $this->repository->findAll();

        $this->assertEquals(count($genres), count($genresDb));
    }

    public function testFindAllEmpty()
    {
        $genresDb = $this->repository->findAll();

        $this->assertCount(0, $genresDb);
    }

    public function testFindAllWithFilter()
    {
        Model::factory()->count(10)->create(['name' => 'Teste']);

        Model::factory()->count(10)->create();

        $genresDb = $this->repository->findAll(
            filter: 'Teste'
        );

        $this->assertEquals(10, count($genresDb));

        $genresDb = $this->repository->findAll();
        $this->assertEquals(20, count($genresDb));
    }

    public function testPagination()
    {
        Model::factory()->count(60)->create();

        $pagination = $this->repository->paginate();

        $this->assertEquals(15, count($pagination->items()));
        $this->assertEquals(60, $pagination->total());

    }

    public function testPaginationEmpty()
    {
        $pagination = $this->repository->paginate();

        $this->assertCount(0, $pagination->items());
        $this->assertEquals(0, $pagination->total());
    }

    public function testUpdate()
    {
        $genre = Model::factory()->create();

        $entity = new EntityGenre(
            id: new Uuid($genre->id),
            name: $genre->name,
            isActive: (bool)$genre->is_active,
            createdAt: new DateTime($genre->created_at)
        );

        $entity->update(
            name: 'Name Updated',
        );

        $response = $this->repository->update($entity);

        $this->assertEquals('Name Updated', $response->name);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Name Updated',
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);
        $genreId = (string) RamseyUuid::uuid4();

        $entity = new EntityGenre(
            id: new Uuid($genreId),
            name: 'Name teste',
            isActive: true,
            createdAt: new DateTime(date('Y-m-d H:i:s'))
        );

        $entity->update(
            name: 'Name Updated',
        );

        $this->repository->update($entity);

    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->delete('fake_id');
    }

    public function testDelete()
    {
        $genre = Model::factory()->create();

        $response = $this->repository->delete($genre->id);

        $this->assertSoftDeleted('genres', [
            'id' => $genre->id
        ]);

        $this->assertTrue($response);

    }

}