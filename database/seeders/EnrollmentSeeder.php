<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        for ($i = 1; $i <= 25; $i++) {
            DB::table('enrollments')->insert([
                'lrn' => str_pad(100000000000 + $i, 12, '0', STR_PAD_LEFT),
                'first_name' => 'Student' . $i,
                'last_name' => 'Test',
                'grade_level' => rand(7, 10),
                'section' => 'Section ' . chr(64 + ($i % 5 + 1)), // A–E
                'academic_year' => '2024-2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
