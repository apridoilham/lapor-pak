<?php

namespace Database\Factories;

use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidentFactory extends Factory
{
    public function definition(): array
    {
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