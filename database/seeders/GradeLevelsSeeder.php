<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeLevel;

class GradeLevelsSeeder extends Seeder
{
    public function run(): void
    {
        $grades = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];

        foreach($grades as $grade){
            GradeLevel::create(['name' => $grade]);
        }
    }
}
