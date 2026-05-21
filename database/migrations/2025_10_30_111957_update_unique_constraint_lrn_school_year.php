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
        $table->dropUnique(['lrn']); // remove old
        $table->unique(['lrn', 'school_year']); // add new
    });
}

public function down()
{
    Schema::table('enrollments', function (Blueprint $table) {
        $table->dropUnique(['lrn', 'school_year']);
        $table->unique('lrn');
    });
}

};
