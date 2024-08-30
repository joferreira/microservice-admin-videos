<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\UseCase\DTO\CastMember\Create\{
    CastMemberCreateInputDto,
    CastMemberCreateOutputDto
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

// iv#G6V4z3xuaP

class CreateCastMemberUseCaseUnitTest extends TestCase
{
    public function test_create()
    {
        // Arrange
        $mockEntity = Mockery::mock(EntityCastMember::class, [ 'name', CastMemberType::DIRECTOR ]);
        $mockEntity->shouldReceive('id');
        $mockEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('insert')->once()->andReturn($mockEntity);
        $useCase = new CreateCastMemberUseCase($mockRepository);

        $mockDto = Mockery::mock(CastMemberCreateInputDto::class, [
            'name', 1
        ]);
        // Action
        $response = $useCase->execute($mockDto);

        // Assert
        $this->assertInstanceOf(CastMemberCreateOutputDto::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('name', $response->name);
        $this->assertEquals(1, $response->type);
        $this->assertNotEmpty($response->created_at);

        Mockery::close();
    }
}