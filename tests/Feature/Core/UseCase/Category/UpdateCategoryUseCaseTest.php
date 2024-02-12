<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category AS Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_update()
    {
        $categoryDb = Model::factory()->create();

        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new UpdateCategoryUseCase($repository);
        $responseUseCase = $useCase->execute(
            new CategoryUpdateInputDto(
                id: $categoryDb->id,
                name: 'name updated',
            )
        );

        $this->assertEquals('name updated', $responseUseCase->name);
        $this->assertEquals($categoryDb->description, $responseUseCase->description);

        $this->assertDatabaseHas('categories', [
            'name' => $responseUseCase->name
        ]);
    }

    public function test_update_description()
    {
        $categoryDb = Model::factory()->create();

        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new UpdateCategoryUseCase($repository);
        $responseUseCase = $useCase->execute(
            new CategoryUpdateInputDto(
                id: $categoryDb->id,
                name: $categoryDb->name,
                description: 'description updated',
            )
        );

        $this->assertEquals($categoryDb->name, $responseUseCase->name);
        $this->assertEquals('description updated', $responseUseCase->description);

        $this->assertDatabaseHas('categories', [
            'name' => $responseUseCase->name,
            'description' => $responseUseCase->description
        ]);
    }
}
