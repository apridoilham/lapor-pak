<?php

namespace Database\Factories;

use App\Enums\ReportVisibilityEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => 'BSBLapor-' . fake()->unique()->randomNumber(6),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(3),
            'image' => 'assets/report/image/default.jpg',
            'latitude' => fake()->latitude(-6.3, -6.4),
            'longitude' => fake()->longitude(106.7, 106.8),
            'address' => fake()->address(),
            'visibility' => fake()->randomElement(ReportVisibilityEnum::cases()),
        ];
    }
}