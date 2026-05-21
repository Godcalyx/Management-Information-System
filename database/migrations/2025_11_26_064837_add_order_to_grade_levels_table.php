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
    Schema::table('grade_levels', function (Blueprint $table) {
        // Step 1: Add column with no unique constraint
        $table->integer('order')->nullable();
    });

    // Step 2: Fill initial order values
    DB::table('grade_levels')->orderBy('id')->get()->each(function ($level, $index) {
        DB::table('grade_levels')
            ->where('id', $level->id)
            ->update(['order' => $index + 1]);
    });

    // Step 3: Add UNIQUE constraint after data is filled
    Schema::table('grade_levels', function (Blueprint $table) {
        $table->unique('order');
    });
}

public function down()
{
    Schema::table('grade_levels', function (Blueprint $table) {
        $table->dropUnique(['order']);
        $table->dropColumn('order');
    });
}

};
