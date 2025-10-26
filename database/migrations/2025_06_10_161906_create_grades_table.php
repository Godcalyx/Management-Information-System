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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // instead of student_id
            $table->unsignedBigInteger('subject_id'); // This is okay
            $table->string('school_year');
            $table->enum('quarter', ['1', '2', '3', '4', 'final']);
            $table->decimal('grade', 5, 2)->nullable();
            $table->enum('status', ['draft', 'transferred', 'approved'])->default('draft');
            $table->timestamps();

            // Foreign key for subject
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
