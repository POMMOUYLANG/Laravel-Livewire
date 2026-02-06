<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    // Change 'void' to 'array'
    public function definition(): array
    {
        $departments = ['Mathematics', 'Science', 'Language', 'History', 'Arts', 'Physical Education'];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'department' => $this->faker->randomElement($departments),
            'subject' => $this->faker->word(),
            'dob' => $this->faker->dateTimeBetween('-60 years', '-25 years'),
            'is_active' => true,
        ];
    }
}
