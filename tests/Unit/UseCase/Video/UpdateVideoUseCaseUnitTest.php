<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\Video\Update\DTO\UpdateInputVideoDTO;
use Core\UseCase\Video\Update\DTO\UpdateOutputVideoDTO;
use Core\UseCase\Video\Update\UpdateVideoUseCase;

use Mockery;

class UpdateVideoUseCaseUnitTest extends BaseVideoUseCaseUnitTest
{
    public function test_exec_input_output()
    {
        $this->createUseCase();

        $response = $this->useCase->exec(
            input: $this->createMockInputDTO()
        );

        $this->assertInstanceOf(UpdateOutputVideoDTO::class, $response);
    }
    
    /**
     * @dataProvider dataProvidersIds
     */
    public function test_exception_categories_ids(
        string $label,
        array $ids
    )
    {
        $this->createUseCase(
            timesCallMethodActionRepository: 0,
            timesCallUpdateMediaRepository: 0,
            timesCallMethodCommitTransaction: 0,
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf(
            '%s %s not found',
            $label,
            implode(', ', $ids)
        ));
        $this->useCase->exec(
            input: $this->createMockInputDTO(
                categoriesIds: $ids
            )
        );
    }

    public function dataProvidersIds(): array
    {
        return [
            ['Category', ['uuid_1']],
            ['Categories', ['uuid_1', 'uuid_2']],
            ['Categories', ['uuid_1', 'uuid_2', 'uuid_3', 'uuid_4']],
        ];
    }

    protected function nameActionRepository(): string
    {
        return 'update';
    }

    protected function getUseCase(): string
    {
        return UpdateVideoUseCase::class;
    }

    protected function createMockInputDTO(
        array $categoriesIds = [],
        array $genresIds = [],
        array $castMembersIds = [],
        ?array $videoFile = null,
        ?array $trailerFile = null,
        ?array $thumbFile = null,
        ?array $thumbHalf = null,
        ?array $bannerFile = null
    ) {
        return Mockery::mock(UpdateInputVideoDTO::class, [
            Uuid::random(),
            'title',
            'description',
            $categoriesIds,
            $genresIds,
            $castMembersIds,
            $videoFile,
            $trailerFile,
            $thumbFile,
            $thumbHalf,
            $bannerFile
        ]);
    }
}
