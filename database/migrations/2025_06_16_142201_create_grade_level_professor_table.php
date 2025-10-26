<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradeLevelProfessorTable extends Migration
{
    public function up(): void
    {
        // database/migrations/xxxx_xx_xx_create_grade_level_professor_table.php
Schema::create('grade_level_professor', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('grade_level'); // e.g., '10', '9', etc.
    $table->unsignedBigInteger('subject_id');
    $table->timestamps();

    // Optional: Add unique constraint to prevent duplicate assignments
    $table->unique(['user_id', 'grade_level', 'subject_id']);

    // Foreign keys
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('grade_level_professor');
    }
}
