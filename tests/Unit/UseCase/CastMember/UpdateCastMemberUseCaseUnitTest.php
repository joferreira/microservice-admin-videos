<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\UseCase\DTO\CastMember\Update\{
    CastMemberUpdateInputDto,
    CastMemberUpdateOutputDto
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use PHPUnit\Framework\TestCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use stdClass;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_update()
    {
        $uuid = (string) RamseyUuid::uuid4();
        $mockEntity = Mockery::mock(EntityCastMember::class, [
            'name',
            CastMemberType::ACTOR,
            new ValueObjectUuid($uuid),
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockEntity->shouldReceive('update');

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('findById')->times(1)->with($uuid)->andReturn($mockEntity);
        $mockRepository->shouldReceive('update')->once()->andReturn($mockEntity);

        $mockInputDto = Mockery::mock(CastMemberUpdateInputDto::class, [
            $uuid,
            'new name',
        ]);

        $useCase = new UpdateCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(CastMemberUpdateOutputDto::class, $response);
    }
}
