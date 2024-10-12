<?php

namespace App\Repositories\Eloquent;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use App\Models\Video as Model;
use App\Repositories\Eloquent\Traits\VideoTrait;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Builder\Video\UpdateVideoBuilder;
use Core\Domain\Entity\{
    Entity,
    Video as VideoEntity
};
use Core\Domain\Enum\MediaStatus;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\{
    Image as ValueObjectImage,
    Media as ValueObjectMedia
};
use Core\Domain\ValueObject\Uuid;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    use VideoTrait;
    
    public function __construct(
        protected Model $model
    ) {}

    public function insert(Entity $entity): Entity
    {
        $entityDb = $this->model->create([
            'id' => $entity->id(),
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'opened' => $entity->opened,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
        ]);

        $this->syncRelationships($entityDb, $entity);

        return $this->convertObjectToEntity($entityDb);
    }

    public function findById(string $id): Entity
    {
        if (!$entityDb = $this->model->find($id)) {
            throw new NotFoundException('Video not found');
        }

        return $this->convertObjectToEntity($entityDb);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $result = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('title', 'LIKE', "%{$filter}%");
                }
            })->orderBy('title', $order)->get();

        return $result->toArray();
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $result = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where('title', 'LIKE', "%{$filter}%");
                }
            })
            ->orderBy('title', $order)
            ->paginate($totalPage, ['*'], 'page', $page);

        return new PaginationPresenter($result);

    }

    public function update(Entity $entity): Entity
    {
        if (!$entityDb = $this->model->find($entity->id())) {
            throw new NotFoundException('Video not found');
        }

        $entityDb->update([
            'title' => $entity->title,
            'description' => $entity->description,
            'year_launched' => $entity->yearLaunched,
            'opened' => $entity->opened,
            'rating' => $entity->rating->value,
            'duration' => $entity->duration,
        ]);

        $entityDb->refresh();

        $this->syncRelationships($entityDb, $entity);        

        return $this->convertObjectToEntity($entityDb);
    }

    public function delete(string $id): bool
    {
        if (!$entityDb = $this->model->find($id)) {
            throw new NotFoundException('Video not found');
        }

        return $entityDb->delete();
    }

    public function updateMedia(Entity $entity): Entity
    {
        if (!$entityDb = $this->model->find($entity->id())) {
            throw new NotFoundException('Video not found');
        }

        $this->updateMediaVideo($entity, $entityDb);
        $this->updateMediaTrailer($entity, $entityDb);
        $this->updateImageBanner($entity, $entityDb);
        $this->updateImageThumb($entity, $entityDb);
        $this->updateImageThumbHalf($entity, $entityDb);

        return $this->convertObjectToEntity($entityDb);
    }

    protected function syncRelationships(Model $model, Entity $entity): void
    {
        $model->categories()->sync($entity->categoriesId);
        $model->genres()->sync($entity->genresId);
        $model->castMembers()->sync($entity->castMemberIds);
    }

    protected function convertObjectToEntity(object $model): VideoEntity
    {
        $entity = new VideoEntity(
            id: new Uuid($model->id),
            title: $model->title,
            description: $model->description,
            yearLaunched: (int) $model->year_launched,
            opened: (bool) $model->opened,
            rating: Rating::from($model->rating),
            duration: $model->duration,
        );

        foreach ($model->categories as $category) {
            $entity->addCategoryId($category->id);
        }

        foreach ($model->genres as $genre) {
            $entity->addGenre($genre->id);
        }

        foreach ($model->castMembers as $castMember) {
            $entity->addCastMember($castMember->id);
        }

        $builder = (new UpdateVideoBuilder())
                        ->setEntity($entity);

        if ($mediaVideo = $model->media) {
            $builder->addMediaVideo(
                path: $mediaVideo->file_path,
                mediaStatus: MediaStatus::from($mediaVideo->media_status),
                encodedPath: $mediaVideo->encoded_path,
            );
        }

        if ($trailer = $model->trailer) {
            $builder->addTrailer($trailer->file_path);
        }

        if ($banner = $model->banner) {
            $builder->addBanner($banner->path);
        }

        if ($thumb = $model->thumb) {
            $builder->addThumb($thumb->path);
        }

        if ($thumbHalf = $model->thumbHalf) {
            $builder->addThumbHalf($thumbHalf->path);
        }

        return $builder->getEntity();
    }
}   