<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RwFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
        ];
    }
}