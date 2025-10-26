<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // ✅ Add this line
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // // Sample subjects
        // DB::table('subjects')->insert(['name' => 'Filipino', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'English', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'Math', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'Science', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'Araling Panlipunan', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'TLE', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'MAPEH', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'Biology', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'ESP', 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('subjects')->insert(['name' => 'Research', 'created_at' => now(), 'updated_at' => now()]);


        // // Sample sections for each grade level
        // DB::table('sections')->insert(['name' => '7-A', 'grade_level' => 7, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '7-B', 'grade_level' => 7, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '8-A', 'grade_level' => 8, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '8-B', 'grade_level' => 8, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '9-A', 'grade_level' => 9, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '9-B', 'grade_level' => 9, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '10-A', 'grade_level' => 10, 'created_at' => now(), 'updated_at' => now()]);
        // DB::table('sections')->insert(['name' => '10-B', 'grade_level' => 10, 'created_at' => now(), 'updated_at' => now()]);

        $this->call(RolesTableSeeder::class);
        $this->call([
        SubjectSeeder::class,
        SectionSeeder::class,
        StudentWithGradesSeeder::class,
        GradeSeeder::class,
        StudentSeeder::class

    ]);
    $this->call(EnrollmentSeeder::class);



    }
    
}

