<?php

namespace Tests\Feature\Core\UseCase\Video;

use App\Models\Video;
use Core\UseCase\Video\Update\DTO\UpdateInputVideoDTO;
use Core\UseCase\Video\Update\UpdateVideoUseCase;

class UpdateVideoUseCaseTest extends BaseVideoUseCase
{
    function useCase(): string
    {
        return UpdateVideoUseCase::class;
    }

    function inputDTO(
        array $categories = [],
        array $genres = [],
        array $castMembers = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    ): object
    {
        $video = Video::factory()->create();
        return new UpdateInputVideoDTO(
            id: $video->id,
            title: 'test',
            description: 'test',
            categories: $categories,
            genres: $genres,
            castMembers: $castMembers,
            videoFile: $videoFile,
            trailerFile: $trailerFile,
            thumbFile: $thumbFile,
            thumbHalf: $thumbHalf,
            bannerFile: $bannerFile
        );
    }
}