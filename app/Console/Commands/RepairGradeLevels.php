<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Grade;
use App\Models\Enrollment;

class RepairGradeLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repair:grade-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix NULL grade_level values in grades table using corresponding enrollments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $grades = Grade::whereNull('grade_level')->get();

        if ($grades->isEmpty()) {
            $this->info('No NULL grade_level found. All grades are correct.');
            return Command::SUCCESS;
        }

        $count = 0;

        foreach ($grades as $grade) {
            // Match enrollment by user_id and school_year
            $enrollment = Enrollment::where('user_id', $grade->user_id)
                ->where('school_year', $grade->school_year)
                ->first();

            if ($enrollment) {
                $grade->grade_level = $enrollment->grade_level;
                $grade->save();
                $count++;
            }
        }

        $this->info("Repaired {$count} old grade records successfully!");
        return Command::SUCCESS;
    }
}
