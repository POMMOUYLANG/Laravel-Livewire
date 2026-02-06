<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'  => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'class' => $this->faker->randomElement(['10A', '10B', '11A', '11B', '12A']),
            'dob'   => $this->faker->date('Y-m-d', '2010-01-01'),
        ];
    }
}
