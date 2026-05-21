<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradesToEnrollmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // GPA (decimal with precision and scale)
            // Example: 4 total digits, 2 decimal places (e.g., 3.99)
            $table->decimal('gpa', 4, 2)->nullable();

            // Weighted Average MSR (decimal)
            // Example: 8 total digits, 2 decimal places
            $table->decimal('weighted_avg_msr', 8, 2)->nullable();

            // Has Failing Grade (boolean)
            $table->boolean('has_failing_grade')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'weighted_avg_msr', 'has_failing_grade']);
        });
    }
}
