<?php

namespace Tests\Feature\Api;

use App\Models\Category as ModelCategory;
use App\Models\Genre as Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected $endpoint = '/api/genres';

    public function testIndexEmpty()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function test_list_all()
    {
        Model::factory()->count(20)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'last_page',
                'first_page',
                'per_page',
                'current_page',
                'to',
                'from'
            ],
        ]);
    }

    public function testStore()
    {
        $categories = ModelCategory::factory()->count(10)->create();

        $response = $this->postJson($this->endpoint, [
            'name' => 'new genre',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function testValidationsStore()
    {
        $categories = ModelCategory::factory()->count(2)->create();

        $payload = [
            'name' => '',
            'is_active' => true,
            'categories_ids' => $categories->pluck('id')->toArray()
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testShowNotFound()
    {
        $response = $this->getJson("{$this->endpoint}/fake_id");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShow()
    {
        $genre = Model::factory()->create();

        $response = $this->getJson("{$this->endpoint}/{$genre->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function testUpdateNotFound()
    {
        $categories = ModelCategory::factory()->count(10)->create();

        $response = $this->putJson("{$this->endpoint}/fake_id", [
            'name' => 'new name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testValidationsUpdate()
    {

        $response = $this->putJson("{$this->endpoint}/fake_value", [
            'name' => 'new name',
            'categories_ids' => []
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['categories_ids']
        ]);
    }

    public function testUpdate()
    {
        $genre = Model::factory()->create();
        $categories = ModelCategory::factory()->count(10)->create();

        $response = $this->putJson("{$this->endpoint}/{$genre->id}", [
            'name' => 'new name',
            'categories_ids' => $categories->pluck('id')->toArray()
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active'
            ]
        ]);
    }

    public function testDeleteNotFound()
    {
        $response = $this->deleteJson("{$this->endpoint}/fake_id");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDelete()
    {
        $genre = Model::factory()->create();

        $response = $this->deleteJson("{$this->endpoint}/{$genre->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
