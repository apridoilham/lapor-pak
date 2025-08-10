<?php

namespace Database\Factories;

use App\Models\Rw;
use Illuminate\Database\Eloquent\Factories\Factory;

class RtFactory extends Factory
{
    public function definition(): array
    {
        return [
            'rw_id' => Rw::factory(),
            'number' => $this->faker->unique()->numerify('###'),
        ];
    }
}