<?php

namespace Database\Factories;

use Carbon\Carbon;
use Core\Domain\Enum\Rating;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'title' => $this->faker->name(),
            'description' => $this->faker->sentence(10),
            'year_launched' => Carbon::make(now()->addYear(5))->format('Y'),
            'opened' => true,
            'rating' => Rating::L,
            'duration' => 10,
            'created_at' => now()
        ];
    }
}
