<?php

namespace Database\Factories;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidentFactory extends Factory
{
    public function definition(): array
    {
        $rw = Rw::factory()->create();
        $rt = Rt::factory()->create(['rw_id' => $rw->id]);

        return [
            'user_id' => User::factory(),
            'avatar' => 'fake-avatar.jpg',
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'address' => $this->faker->streetAddress(),
        ];
    }
}