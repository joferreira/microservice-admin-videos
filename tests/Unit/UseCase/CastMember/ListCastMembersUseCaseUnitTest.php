<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\UseCase\DTO\CastMember\List\{
    ListCastMembersInputDto,
    ListCastMembersOutputDto
};
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\UseCase\UseCaseTrait;

class ListCastMembersUseCaseUnitTest extends TestCase
{
    use UseCaseTrait;

    public function test_list()
    {

        $mockRepository = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive('paginate')->once()->andReturn($this->mockPagination());

        $mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [
            'filter', 'DESC', 1, 15
        ]);

        $useCase = new ListCastMembersUseCase($mockRepository);
        $response = $useCase->execute($mockInputDto);

        $this->assertInstanceOf(ListCastMembersOutputDto::class, $response);

        Mockery::close();
    }
}