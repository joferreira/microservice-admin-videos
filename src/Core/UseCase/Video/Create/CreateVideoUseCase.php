<?php

namespace Core\UseCase\Video\Create;

use Core\Domain\Entity\Video as Entity;
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Events\VideoCreatedEvent;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Media;
use Core\UseCase\Interfaces\FileStorageInterface;
use Core\UseCase\Interfaces\TransactionInterface;
use Core\UseCase\Video\Create\DTO\CreateInputVideoDTO;
use Core\UseCase\Video\Create\DTO\CreateOutputVideoDTO;
use Core\UseCase\Video\Interfaces\VideoEventManagerInterface;
use Throwable;

class CreateVideoUseCase
{
    public function __construct(
        protected VideoRepositoryInterface $repository,
        protected TransactionInterface $transaction,
        protected FileStorageInterface $storage,
        protected VideoEventManagerInterface $eventManager,

        protected CategoryRepositoryInterface $repositoryCategory,
        protected GenreRepositoryInterface $repositoryGenre,
        protected CastMemberRepositoryInterface $repositoryCastMember,
    ) {
    }

    public function exec(CreateInputVideoDTO $input): CreateOutputVideoDTO
    {
        $entity = $this->createEntity($input);
        
        try {
            $this->repository->insert($entity);

            if ($pathMedia = $this->storeMedia($entity->id(), $input->videoFile)) {
                $media = new Media(
                    filePath: $pathMedia,
                    mediaStatus: MediaStatus::PROCESSING
                );
                $entity->setVideoFile($media);
                $this->repository->updateMedia($entity);

                $this->eventManager->dispatch(new VideoCreatedEvent($entity));
            }
            
            $this->transaction->commit();

            return $this->output($entity);
        } catch (Throwable $th) {
            $this->transaction->rollback();

            // if (isset($pathMedia)) {
            //     $this->storage->delete($pathMedia);
            // }

            throw $th;
        }
    }

    private function createEntity(CreateInputVideoDTO $input): Entity
    {
        // create entity -> $input
        $entity = new Entity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: true,
            rating: $input->rating
        );

        $this->validateCategoriesId($input->categories);
        // add categories_ids in entity - validate
        foreach ($input->categories as $categoryId) {
            $entity->addCategoryId($categoryId);
        }

        $this->validateGenresId($input->genres);
        // add genres_ids in entity - validate
        foreach ($input->genres as $genreId) {
            $entity->addGenre($genreId);
        }

        $this->validateCastMembersId($input->castMembers);
        // add cast_members_ids in entity - validate
        foreach ($input->castMembers as $castMemberId) {
            $entity->addCastMember($castMemberId);
        }

        return $entity;
    }

    private function storeMedia(string $path, ?array $media = null): string
    {
        if ($media) {
            $this->storage->store(
                path: $path,
                file: $media,
            );
        }

        return '';
    }

    private function validateCategoriesId(array $categoriesId = [])
    {
        $categoriesDb = $this->repositoryCategory->getIdsListIds($categoriesId);

        $arrayDiff = array_diff($categoriesId, $categoriesDb);

        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'Categories' : 'Category',
                implode(', ', $arrayDiff)
            );

            throw new NotFoundException($msg);
        }
    }

    private function validateGenresId(array $genresId = [])
    {
        $genresDb = $this->repositoryGenre->getIdsListIds($genresId);
    
        $arrayDiff = array_diff($genresId, $genresDb);
    
        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'Genres' : 'Genre',
                implode(', ', $arrayDiff)
            );
    
            throw new NotFoundException($msg);
        }
    }
    private function validateCastMembersId(array $castMembersId = [])
    {
        $castMembersDb = $this->repositoryCastMember->getIdsListIds($castMembersId);

        $arrayDiff = array_diff($castMembersId, $castMembersDb);

        if (count($arrayDiff)) {
            $msg = sprintf(
                '%s %s not found',
                count($arrayDiff) > 1 ? 'CastMembers' : 'CastMember',
                implode(', ', $arrayDiff)
            );

            throw new NotFoundException($msg);
        }
    }

    private function output(Entity $entity): CreateOutputVideoDTO
    {
        return new CreateOutputVideoDTO(
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
            thumbFile: $entity->thumbFile()?->filePath,
            thumbHalf: $entity->thumbHalf()?->filePath,
            bannerFile: $entity->bannerFile()?->filePath,
        );
    }
}