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
    Schema::table('users', function (Blueprint $table) {
        $table->date('birthdate')->nullable();
        $table->string('sex', 10)->nullable();
        $table->string('grade_level')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['birthdate', 'sex', 'grade_level']);
    });
}

};
