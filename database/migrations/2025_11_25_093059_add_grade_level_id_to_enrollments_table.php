<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('grade_level_id')->after('school_year')->nullable();

            // Optional: Add foreign key
            $table->foreign('grade_level_id')->references('id')->on('grade_levels')->onDelete('cascade');

            // You can drop old grade_level column later after migration
            // $table->dropColumn('grade_level');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['grade_level_id']);
            $table->dropColumn('grade_level_id');
        });
    }
};
