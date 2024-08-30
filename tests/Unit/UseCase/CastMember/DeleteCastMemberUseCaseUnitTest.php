<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDto;
use Core\UseCase\DTO\CastMember\Delete\CastMemberDeleteOutputDto;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Mockery;
use stdClass;

class DeleteCastMemberUseCaseUnitTest extends TestCase
{
    public function test_delete()
    {
        $uuid = (string) RamseyUuid::uuid4();
        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('delete')->once()->andReturn(true);

        $mockInputDto = Mockery::mock(CastMemberInputDto::class, [$uuid]);

        $useCase = new DeleteCastMemberUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);
        
        $this->assertInstanceOf(CastMemberDeleteOutputDto::class, $response);
        $this->assertTrue($response->success);

        Mockery::close();
    }
}
