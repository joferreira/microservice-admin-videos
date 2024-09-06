<?php

namespace Core\UseCase\Video\List;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\Video\List\DTO\ListInputVideoDTO;
use Core\UseCase\Video\List\DTO\ListOutputVideoDTO;

class ListVideoUseCase
{
    public function __construct(
        private VideoRepositoryInterface $repository
    ) {}

    public function exec(ListInputVideoDTO $input): ListOutputVideoDTO
    {
        $entity = $this->repository->findById($input->id);

        return new ListOutputVideoDTO(
            id: $entity->id(),
            title: $entity->title,
            description: $entity->description,
            yearLaunched: $entity->yearLaunched,
            duration: $entity->duration,
            opened: $entity->opened,
            rating: $entity->rating,
            categories: $entity->categoriesId,
            genres: $entity->genresId,
            castMembers: $entity->castMemberIds,
            videoFile: $entity->videoFile()?->filePath,
            trailerFile: $entity->trailerFile()?->filePath,
            thumbFile: $entity->thumbFile()?->path(),
            thumbHalf: $entity->thumbHalf()?->path(),
            bannerFile: $entity->bannerFile()?->path(),
        );
    }
}