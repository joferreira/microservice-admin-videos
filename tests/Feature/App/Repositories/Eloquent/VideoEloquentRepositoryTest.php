<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\{
    CastMember,
    Category,
    Genre,
    Video as Model
};
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Domain\Entity\Video as EntityVideo;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VideoEloquentRepositoryTest extends TestCase
{
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new VideoEloquentRepository(
            new Model()
        );
    }

    public function testImplementsInterface()
    {
       $this->assertInstanceOf(VideoRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new EntityVideo(
            title: 'title',
            description: 'description',
            yearLaunched: 2021,
            opened: true,
            rating: Rating::L,
            duration: 90,
        );

        $this->repository->insert($entity);

        $this->assertDatabaseHas('videos', [
            'id' => $entity->id(),
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2021,
            'opened' => true,
            'rating' => Rating::L,
            'duration' => 90,
        ]);
    }

    public function testInsertWithRelationships()
    {
        $categories = Category::factory()->count(4)->create();
        $genres = Genre::factory()->count(4)->create();
        $castMembers = CastMember::factory()->count(4)->create();

        $entity = new EntityVideo(
            title: 'title',
            description: 'description',
            yearLaunched: 2021,
            opened: true,
            rating: Rating::L,
            duration: 90,
        );

        foreach ($categories as $category) {
            $entity->addCategoryId($category->id);
        }

        foreach ($genres as $genre) {
            $entity->addGenre($genre->id);
        }

        foreach ($castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $entityInDb = $this->repository->insert($entity);

        $this->assertDatabaseHas('videos', [
            'id' => $entity->id(),
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2021,
            'opened' => true,
            'rating' => Rating::L,
            'duration' => 90,
        ]);

        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertDatabaseCount('cast_member_video', 4);

        $this->assertCount(4, $entityInDb->categoriesId);
        $this->assertCount(4, $entityInDb->genresId);
        $this->assertCount(4, $entityInDb->castMemberIds);

        $this->assertEquals($categories->pluck('id')->toArray(), $entityInDb->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $entityInDb->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $entityInDb->castMemberIds);
    }

    public function testNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->findById('fake-value');

    }

    public function testFindById()
    {
        $video = Model::factory()->create();

        $response = $this->repository->findById($video->id);

        $this->assertEquals($video->id, $response->id());
        $this->assertEquals($video->title, $response->title);
    }

    public function testFindAll()
    {
        Model::factory()->count(10)->create();

        $response = $this->repository->findAll();

        $this->assertCount(10, $response);
    }

    public function testFindAllWithFilter()
    {
        Model::factory()->count(10)->create();
        Model::factory()->count(10)->create([
            'title' => 'teste'
        ]);

        $response = $this->repository->findAll(filter: 'teste');

        $this->assertCount(10, $response);
        $this->assertDatabaseCount('videos', 20);
    }

    /**
     * @dataProvider dataProviderPaginate
     */
    public function testPaginate(
        int $page,
        int $totalPage,
        int $total = 50
    )
    {
        Model::factory()->count($total)->create();

        $response = $this->repository->paginate(
            page: $page,
            totalPage: $totalPage
        );

        $this->assertCount($totalPage, $response->items());
        $this->assertEquals($total, $response->total());
        $this->assertEquals($page, $response->currentPage());
        $this->assertEquals($totalPage, $response->perPage());
    }

    protected function dataProviderPaginate(): array
    {
        return [
            [
                'page' => 1,
                'totalPage' => 10,
                'total' => 100
            ],
            [
                'page' => 2,
                'totalPage' => 15
            ],
            [
                'page' => 3,
                'totalPage' => 15
            ]
        ];
    }

    public function testUpdateNotFount()
    {   
        $this->expectException(NotFoundException::class);

        $entity = new EntityVideo(
            title: 'title',
            description: 'description',
            yearLaunched: 2021,
            opened: true,
            rating: Rating::L,
            duration: 90,
        );

        $this->repository->update($entity);
    }

    public function testUpdate()
    {
        $categories = Category::factory()->count(10)->create();
        $genres = Genre::factory()->count(10)->create();
        $castMembers = CastMember::factory()->count(10)->create();

        $videoDb = Model::factory()->create();

        $this->assertDatabaseHas('videos', [
            'title' => $videoDb->title,
        ]);

        $entity = new EntityVideo(
            id: new Uuid($videoDb->id),
            title: 'teste',
            description: 'description',
            yearLaunched: 2021,
            opened: true,
            rating: Rating::L,
            duration: 90,
            createdAt: new DateTime($videoDb->created_at),
        );

        foreach ($categories as $category) {
            $entity->addCategoryId($category->id);
        }

        foreach ($genres as $genre) {
            $entity->addGenre($genre->id);
        }

        foreach ($castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $entityInDb = $this->repository->update($entity);

        $this->assertDatabaseHas('videos', [
            'title' => 'teste',
        ]);

        $this->assertDatabaseCount('category_video', 10);
        $this->assertDatabaseCount('genre_video', 10);
        $this->assertDatabaseCount('cast_member_video', 10);

        $this->assertEquals($categories->pluck('id')->toArray(), $entityInDb->categoriesId);
        $this->assertEquals($genres->pluck('id')->toArray(), $entityInDb->genresId);
        $this->assertEquals($castMembers->pluck('id')->toArray(), $entityInDb->castMemberIds);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->repository->delete('fake-value');
    }

    public function testDelete()
    {
        $video = Model::factory()->create();

        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
        ]);

        $this->repository->delete($video->id);

        $this->assertSoftDeleted('videos', [
            'id' => $video->id,
        ]);
    }
    
}
