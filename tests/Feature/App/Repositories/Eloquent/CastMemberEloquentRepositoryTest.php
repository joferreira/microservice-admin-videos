<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use Core\Domain\Exception\NotFoundException;
use App\Models\CastMember as Model;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMember as Entity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;


class CastMemberEloquentRepositoryTest extends TestCase
{
    protected $repository;
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new Model());
    }

    public function testCheckImplementsInterfaceRepository()
    {
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'Teste',
            type: CastMemberType::ACTOR,
        );

        $response = $this->repository->insert($entity);

        $this->assertDatabaseHas('cast_members', [
            'id'=> $response->id(),
            'name'=> $entity->name,
            'type'=> $entity->type
        ]);
        $this->assertEquals($entity->name, $response->name);
    }

    public function testFindByIdNotFound()
    {
        try {
            $response = $this->repository->findById('idfake');
        } catch (Throwable $th) {
            $this->assertInstanceOf(NotFoundException::class, $th);
        }
    }

    public function testFindById()
    {
        $castMember = Model::factory()->create();

        $response = $this->repository->findById($castMember->id);

        $this->assertEquals($castMember->id, $response->id());
        $this->assertEquals($castMember->name, $response->name);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertCount(0, $response);
    }

    public function testFindAll()
    {
        $castMembers = Model::factory()->count(50)->create();
        
        $response = $this->repository->findAll();

        $this->assertEquals(count($castMembers), count($response));
    }

    public function testPaginate()
    {
        Model::factory()->count(20)->create();
        
        $response = $this->repository->paginate();

        $this->assertCount(15, $response->items());
        $this->assertEquals(20, $response->total());
    }

    public function testPaginatePageTwo()
    {
        Model::factory()->count(80)->create();

        $response = $this->repository->paginate(
            totalPage: 10
        );

        $this->assertCount(10, $response->items());
        $this->assertEquals(80, $response->total());
    }

    public function testUpdateNotFound()
    {
        $this->expectException(NotFoundException::class);

        $entity = new Entity(
            name: 'Teste',
            type: CastMemberType::DIRECTOR,
        );

        $this->repository->update($entity);
    }

    public function testUpdate()
    {
        $castMember = Model::factory()->create();

        $entity = new Entity(
            id: new ValueObjectUuid($castMember->id),
            name: 'Teste',
            type: CastMemberType::DIRECTOR,
        );

        $response = $this->repository->update($entity);

        $this->assertNotEquals($castMember->name, $response->name);
        $this->assertEquals('Teste', $response->name);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->repository->delete('idfake');
    }

    public function testDelete()
    {
        $castMember = Model::factory()->create();

        $this->repository->delete($castMember->id);

        $this->assertSoftDeleted('cast_members', [
            'id'=> $castMember->id
        ]);
    }

}
