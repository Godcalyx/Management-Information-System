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
        $table->unsignedBigInteger('student_id');

        // Optional: enforce the foreign key constraint
        $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            //
        });
    }
};
