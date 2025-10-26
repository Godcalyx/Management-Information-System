<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Subject;
use App\Models\Section;

class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject_id' => Subject::inRandomOrder()->first()->id ?? 1,
            'section_id' => Section::inRandomOrder()->first()->id ?? 1,
            'quarter' => $this->faker->randomElement(['Q1', 'Q2', 'Q3', 'Q4']),
            'school_year' => $this->faker->randomElement(['2022-2023', '2023-2024']),
            'grade' => $this->faker->randomFloat(2, 75, 100),
            'submitted_by' => User::where('role', 'professor')->inRandomOrder()->first()->id ?? 2,
            'approved_by' => null,
            'status' => $this->faker->randomElement(['draft', 'submitted']),
        ];
    }
}
