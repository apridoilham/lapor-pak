<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RtFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => str_pad(fake()->numberBetween(1, 20), 3, '0', STR_PAD_LEFT),
            'rw_id' => \App\Models\Rw::factory(),
        ];
    }
}