<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RwFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => $this->faker->unique()->numerify('###'),
        ];
    }
}