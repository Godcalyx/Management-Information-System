<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->enum('promotion_status', ['pending', 'eligible', 'inc', 'completed'])
              ->default('pending')
              ->after('status');
        $table->text('promotion_notes')->nullable();
        $table->decimal('gpa', 5, 2)->nullable()->after('completion_status');
        $table->decimal('weighted_avg_msr', 5, 2)->nullable()->after('gpa');
        $table->boolean('has_failing_grade')->default(false)->after('weighted_avg_msr');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
$table->dropColumn(['gpa', 'weighted_avg_msr', 'has_failing_grade']);
});
    }
};
