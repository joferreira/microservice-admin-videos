<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\Delete\{
    DeleteVideoUseCase,
    DTO\DeleteInputVideoDTO
};
use Tests\TestCase;

class DeleteUseCaseTest extends TestCase
{

    public function test_delete()
    {
        $video = Video::factory()->create();

        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $response = $useCase->exec(new DeleteInputVideoDTO(
            id: $video->id
        ));

        $this->assertTrue($response->deleted);
    }

    public function test_delete_not_found()
    {
        $this->expectException(NotFoundException::class);

        $useCase = new DeleteVideoUseCase(
            $this->app->make(VideoRepositoryInterface::class)
        );

        $useCase->exec(new DeleteInputVideoDTO(
            id: 'fake_id'
        ));

    }
}
