<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\DTO\ListInputVideoDTO;
use Core\UseCase\Video\List\ListVideoUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListVideoUseCaseTest extends TestCase
{

    public function test_list()
    {
        $video = Video::factory()->create();

        $useCase = new ListVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $response = $useCase->exec(new ListInputVideoDTO(
            id: $video->id,
        ));

        $this->assertNotNull($response);
        $this->assertEquals($video->id, $response->id);
    }

    public function test_list_not_found()
    {
        $this->expectException(NotFoundException::class);

        $useCase = new ListVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class),
        );

        $useCase->exec(new ListInputVideoDTO(
            id: 'fake-id',
        ));


    }
}
