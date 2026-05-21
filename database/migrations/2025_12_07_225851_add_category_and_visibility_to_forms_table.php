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
    Schema::table('forms', function (Blueprint $table) {
        $table->enum('visibility', ['public', 'admin', 'professor', 'student'])->default('public')->after('category');
    });
}

public function down()
{
    Schema::table('forms', function (Blueprint $table) {
        $table->dropColumn(['category', 'visibility']);
    });
}

};
