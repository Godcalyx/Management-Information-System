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
    Schema::table('advisories', function (Blueprint $table) {
        $table->unique(['user_id', 'grade_level_id', 'school_year'], 'unique_advisory');
    });
}

public function down()
{
    Schema::table('advisories', function (Blueprint $table) {
        $table->dropUnique('unique_advisory');
    });
}

};
