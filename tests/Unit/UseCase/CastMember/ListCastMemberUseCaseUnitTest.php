<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class ListCastMemberUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_list()
    {
        $uuid = (string) RamseyUuid::uuid4();
        $mockEntity = Mockery::mock(EntityCastMember::class, [
            'name',
            CastMemberType::ACTOR,
            new Uuid($uuid),
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->times(1)->with($uuid)->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CastMemberInputDto::class, [$uuid]);

        $useCase = new ListCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberOutputDto::class, $response);

        Mockery::close();
    }
}
