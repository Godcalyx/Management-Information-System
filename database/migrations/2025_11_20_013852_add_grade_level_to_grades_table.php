<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Grade;
use App\Models\Enrollment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            // Add grade_level column
            $table->integer('grade_level')->nullable()->after('grade');
        });

        // ---- AUTO-FILL grade_level FOR EXISTING GRADES ----
        // We match grades to enrollment records using:
        // user_id + school_year
        $grades = Grade::all();

        foreach ($grades as $grade) {

            $enrollment = Enrollment::where('user_id', $grade->user_id)
                ->where('school_year', $grade->school_year) // VERY IMPORTANT
                ->orderBy('id', 'desc')
                ->first();

            if ($enrollment) {
                $grade->grade_level = $enrollment->grade_level;
                $grade->save();
            }
        }
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('grade_level');
        });
    }
};
