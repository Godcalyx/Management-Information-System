<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        DB::table('subjects')->insert([
            ['id' => 1,  'name' => 'Filipino', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 2,  'name' => 'English', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 3,  'name' => 'Mathematics', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 4,  'name' => 'Science', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 5,  'name' => 'Araling Panlipunan', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 6,  'name' => 'Technology and Livelihood Education', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 7,  'name' => 'Music, Arts, PE and Health', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 8,  'name' => 'Edukasyon sa Pagpapakatao', 'grade_level' => 7, 'version' => null, 'prerequisite_id' => null],
            ['id' => 9,  'name' => 'Environmental Science', 'grade_level' => 7, 'version' => 'Science 2', 'prerequisite_id' => 4],
            ['id' => 10, 'name' => 'Filipino', 'grade_level' => 8, 'version' => 'Filipino 2', 'prerequisite_id' => 1],
            ['id' => 11, 'name' => 'English', 'grade_level' => 8, 'version' => 'English 2', 'prerequisite_id' => 2],
            ['id' => 12, 'name' => 'Mathematics', 'grade_level' => 8, 'version' => 'Mathematics 2', 'prerequisite_id' => 3],
            ['id' => 13, 'name' => 'Science', 'grade_level' => 8, 'version' => 'Science 3', 'prerequisite_id' => 9],
            ['id' => 14, 'name' => 'Araling Panlipunan', 'grade_level' => 8, 'version' => 'AP 2', 'prerequisite_id' => 5],
            ['id' => 15, 'name' => 'Technology and Livelihood Education', 'grade_level' => 8, 'version' => 'TLE 2', 'prerequisite_id' => 6],
            ['id' => 16, 'name' => 'Music, Arts, PE and Health', 'grade_level' => 8, 'version' => 'MAPEH 2', 'prerequisite_id' => 7],
            ['id' => 17, 'name' => 'Edukasyon sa Pagpapakatao', 'grade_level' => 8, 'version' => 'ESP 2', 'prerequisite_id' => 8],
            ['id' => 18, 'name' => 'Mathematics', 'grade_level' => 8, 'version' => 'Mathematics 3', 'prerequisite_id' => 12],
            ['id' => 19, 'name' => 'Research', 'grade_level' => 8, 'version' => 'Research 1', 'prerequisite_id' => null],
            ['id' => 20, 'name' => 'Filipino', 'grade_level' => 9, 'version' => 'Filipino 3', 'prerequisite_id' => 10],
            ['id' => 21, 'name' => 'English', 'grade_level' => 9, 'version' => 'English 3', 'prerequisite_id' => 11],
            ['id' => 22, 'name' => 'Mathematics', 'grade_level' => 9, 'version' => 'Mathematics 4', 'prerequisite_id' => 18],
            ['id' => 23, 'name' => 'Science', 'grade_level' => 9, 'version' => 'Science 4', 'prerequisite_id' => 13],
            ['id' => 24, 'name' => 'Araling Panlipunan', 'grade_level' => 9, 'version' => 'AP 3', 'prerequisite_id' => 14],
            ['id' => 25, 'name' => 'Technology and Livelihood Education', 'grade_level' => 9, 'version' => 'TLE 3', 'prerequisite_id' => 15],
            ['id' => 26, 'name' => 'Music, Arts, PE and Health', 'grade_level' => 9, 'version' => 'MAPEH 3', 'prerequisite_id' => 16],
            ['id' => 27, 'name' => 'Edukasyon sa Pagpapakatao', 'grade_level' => 9, 'version' => 'ESP 3', 'prerequisite_id' => 17],
            ['id' => 28, 'name' => 'Mathematics', 'grade_level' => 9, 'version' => 'Statistics', 'prerequisite_id' => 22],
            ['id' => 29, 'name' => 'Research', 'grade_level' => 9, 'version' => 'Research 2', 'prerequisite_id' => 19],
            ['id' => 30, 'name' => 'Filipino', 'grade_level' => 10, 'version' => 'Filipino 4', 'prerequisite_id' => 20],
            ['id' => 31, 'name' => 'English', 'grade_level' => 10, 'version' => 'English 4', 'prerequisite_id' => 21],
            ['id' => 32, 'name' => 'Mathematics', 'grade_level' => 10, 'version' => 'Mathematics 6', 'prerequisite_id' => 28],
            ['id' => 33, 'name' => 'Science', 'grade_level' => 10, 'version' => 'Science 5', 'prerequisite_id' => 23],
            ['id' => 34, 'name' => 'Araling Panlipunan', 'grade_level' => 10, 'version' => 'AP 4', 'prerequisite_id' => 24],
            ['id' => 35, 'name' => 'Technology and Livelihood Education', 'grade_level' => 10, 'version' => 'TLE 4', 'prerequisite_id' => 25],
            ['id' => 36, 'name' => 'Music, Arts, PE and Health', 'grade_level' => 10, 'version' => 'MAPEH 4', 'prerequisite_id' => 26],
            ['id' => 37, 'name' => 'Edukasyon sa Pagpapakatao', 'grade_level' => 10, 'version' => 'ESP 4', 'prerequisite_id' => 27],
            ['id' => 38, 'name' => 'Advanced Biology', 'grade_level' => 10, 'version' => 'Bio Adv', 'prerequisite_id' => 33],
            ['id' => 39, 'name' => 'Research', 'grade_level' => 10, 'version' => 'Research 3', 'prerequisite_id' => 29],
        ]);
    }
}
