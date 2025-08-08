<?php

namespace Database\Factories;

use App\Models\ReportCategory;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'resident_id' => Resident::factory(),
            'report_category_id' => ReportCategory::factory(),
            'code' => 'BSBLapor-' . Str::upper(Str::random(6)),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'image' => 'fake-report-image.jpg',
            'latitude' => (string) $this->faker->latitude(),
            'longitude' => (string) $this->faker->longitude(),
            'address' => $this->faker->address(),
        ];
    }
}