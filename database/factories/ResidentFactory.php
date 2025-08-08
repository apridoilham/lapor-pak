<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'avatar' => 'fake-avatar.jpg',
        ];
    }
}