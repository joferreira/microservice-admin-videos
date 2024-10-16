<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember;
use Core\Domain\Entity\Entity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{
    protected $model;
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function insert(Entity $castMember): Entity
    {
        $modelDb = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
            'created_at' => $castMember->createdAt(),
        ]);

        return $this->convertToEntity($modelDb);
    }

    public function findById(string $castMemberId): CastMember
    {
        if (!$modelDb = $this->model->find($castMemberId)) {
            throw new NotFoundException("Cast Member {$castMemberId} not found");
        }

        return $this->convertToEntity($modelDb);
    }

    public function getIdsListIds(array $castMembersIds = []): array
    {
        return $this->model
            ->whereIn('id', $castMembersIds)
            ->pluck('id')
            ->toArray();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $dataDb = $this->model
                        ->where(function ($query) use ($filter) {
                            if ($filter) {
                                $query->where('name', 'LIKE', "%{$filter}%");
                            }
                        })
                        ->orderBy('name', $order)
                        ->get();
        return $dataDb->toArray();        
    }

    public function paginate(string $filter = '', $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $query = $this->model;
        if ($filter) {
            $query = $query->where('name', 'LIKE', "%{$filter}%");
        }

        $query = $query->orderBy('name', $order);
        $dataDb = $query->paginate($totalPage);

        return new PaginationPresenter($dataDb);
    }

    public function update(Entity $castMember): Entity
    {
        if (!$dataDb = $this->model->find($castMember->id())) {
            throw new NotFoundException("Cast Member {$castMember->id()} not found");
        }

        $dataDb->update([
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);

        $dataDb->refresh();

        return $this->convertToEntity($dataDb);
    }

    public function delete(string $castMemberId): bool
    {
        if (!$dataDb = $this->model->find($castMemberId)) {
            throw new NotFoundException("Cast Member {$castMemberId} not found");
        }

        return $dataDb->delete();        
    }

    private function convertToEntity(Model $model): CastMember
    {
        return new CastMember(
            id: new ValueObjectUuid($model->id),
            name: $model->name,
            type: CastMemberType::from($model->type),
            createdAt: $model->created_at,
        );
    }
}