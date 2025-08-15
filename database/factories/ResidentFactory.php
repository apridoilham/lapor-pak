<?php

namespace Database\Factories;

use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Resident>
 */
class ResidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Pastikan ada data RW dan RT untuk di-assign
        $rw = Rw::factory()->create();
        $rt = Rt::factory()->create(['rw_id' => $rw->id]);

        return [
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
        ];
    }
}