<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\{
    CastMemberRepositoryInterface,
    CategoryRepositoryInterface,
    GenreRepositoryInterface,
    VideoRepositoryInterface
};
use Core\UseCase\Interfaces\{
    FileStorageInterface,
    TransactionInterface
};
use Core\UseCase\Video\Create\CreateVideoUseCase;
use Core\UseCase\Video\Create\DTO\CreateInputVideoDTO;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\Stubs\UploadFilesStub;
use Tests\Stubs\VideoEventStub;
use Tests\TestCase;

abstract class BaseVideoUseCase extends TestCase
{
    abstract function useCase(): string;
    abstract function inputDTO(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    ): object;

    /**
     * @dataProvider provider
     */
    public function test_create(
        int $categories,
        int $genres,
        int $castMembers,
        bool $withMediaVideo = false,
        bool $withMediaTrailer = false,
        bool $withMediaThumb = false,
        bool $withMediaThumbHalf = false,
        bool $withMediaBanner = false
    )
    {
        $useCase = new ($this->useCase())(
            $this->app->make(VideoRepositoryInterface::class),
            $this->app->make(TransactionInterface::class),
            // $this->app->make(FileStorageInterface::class),
            new UploadFilesStub(),
            // $this->app->make(VideoEventManagerInterface::class),
            new VideoEventStub(),
            $this->app->make(CategoryRepositoryInterface::class),
            $this->app->make(GenreRepositoryInterface::class),
            $this->app->make(CastMemberRepositoryInterface::class),
        );

        $categoriesIds = Category::factory()->count($categories)->create()->pluck('id')->toArray();
        $genresIds = Genre::factory()->count($genres)->create()->pluck('id')->toArray();
        $castMembersIds = CastMember::factory()->count($castMembers)->create()->pluck('id')->toArray();

        $fakeFile = UploadedFile::fake()->create('video.mp4', 1, 'video/mp4');
        $file = [
            'tmp_name' => $fakeFile->getPathname(),
            'name' => $fakeFile->getFilename(),
            'type' => $fakeFile->getMimeType(),
            'error' => $fakeFile->getError()
        ];

        $input = $this->inputDTO(
            categories: $categoriesIds,
            genres: $genresIds,
            castMembers: $castMembersIds,
            videoFile: $withMediaVideo ? $file : null,
            trailerFile: $withMediaTrailer ? $file : null,
            thumbFile: $withMediaThumb ? $file : null,
            thumbHalf: $withMediaThumbHalf ? $file : null,
            bannerFile: $withMediaBanner ? $file : null
        );

        $response = $useCase->exec($input);

        $this->assertEquals($input->title, $response->title);
        $this->assertEquals($input->description, $response->description);
        // $this->assertEquals($input->yearLaunched, $response->yearLaunched);
        // $this->assertEquals($input->duration, $response->duration);
        // $this->assertEquals($input->opened, $response->opened);
        // $this->assertEquals($input->rating, $response->rating);

        $this->assertCount($categories, $response->categories);
        $this->assertEqualsCanonicalizing($input->categories, $response->categories);
        $this->assertCount($genres, $response->genres);
        $this->assertEqualsCanonicalizing($input->genres, $response->genres);
        $this->assertCount($castMembers, $response->castMembers);
        $this->assertEqualsCanonicalizing($input->castMembers, $response->castMembers);

        $this->assertTrue($withMediaVideo ? $response->videoFile !== null : $response->videoFile === null);
        $this->assertTrue($withMediaTrailer ? $response->trailerFile !== null : $response->trailerFile === null);
        $this->assertTrue($withMediaThumb ? $response->thumbFile !== null : $response->thumbFile === null);
        $this->assertTrue($withMediaThumbHalf ? $response->thumbHalf !== null : $response->thumbHalf === null);
        $this->assertTrue($withMediaBanner ? $response->bannerFile !== null : $response->bannerFile === null);

    }

    protected function provider(): array
    {
        return [
            'Test with all IDs and media video' =>
            [ 
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 3,
                'withMediaVideo' => true,
                'withMediaTrailer' => false,
                'withMediaThumb' => false,
                'withMediaThumbHalf' => false,
                'withMediaBanner' => false
            ],
            'Test with categories and genres and without file' =>
            [
                'categories' => 3,
                'genres' => 3,
                'castMembers' => 0
            ],
            'Test with all IDs and all videos' =>
            [
                'categories' => 2,
                'genres' => 2,
                'castMembers' => 2,
                'withMediaVideo' => true,
                'withMediaTrailer' => true,
                'withMediaThumb' => true,
                'withMediaThumbHalf' => true,
                'withMediaBanner' => true
            ],
            'Test without IDs and all videos' =>
            [
                'categories' => 0,
                'genres' => 0,
                'castMembers' => 0,
                'withMediaVideo' => true,
                'withMediaTrailer' => true,
                'withMediaThumb' => true,
                'withMediaThumbHalf' => true,
                'withMediaBanner' => true
            ],
        ];
    }
}
