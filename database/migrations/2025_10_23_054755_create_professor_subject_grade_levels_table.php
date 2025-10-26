<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('professor_subject_grade_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // renamed from professor_id → user_id
            $table->unsignedBigInteger('subject_id');
            $table->string('grade_level');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_subject_grade_levels');
    }
};
