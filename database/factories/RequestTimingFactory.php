<?php

namespace Database\Factories;

use App\Models\RequestTiming;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestTimingFactory extends Factory
{
    protected $model = RequestTiming::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'duration' => $this->faker->randomFloat(6, 0.001, 5.0),
            'created_at' => now(),
        ];
    }
}
