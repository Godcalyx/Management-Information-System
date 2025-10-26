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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            
            // Link to users table
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('lrn')->nullable()->unique();

            $table->string('school_year');
            $table->string('grade_level');

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('extension_name')->nullable();

            $table->date('birthdate');
            $table->string('birthplace');
            $table->enum('sex', ['Male', 'Female']); // Optional enum for sex

            $table->string('mother_tongue')->nullable();
            $table->string('ip_community')->nullable();
            $table->string('ip_specify')->nullable();

            $table->string('is_4ps')->nullable(); // You can use enum here as well, e.g. ['Yes', 'No']

            $table->string('household_id')->nullable();

            // Current address
            $table->string('current_house')->nullable();
            $table->string('current_street')->nullable();
            $table->string('current_barangay')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_province')->nullable();
            $table->string('current_country')->nullable();
            $table->string('current_zip')->nullable();

            // Permanent address
            $table->string('permanent_house')->nullable();
            $table->string('permanent_street')->nullable();
            $table->string('permanent_barangay')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_province')->nullable();
            $table->string('permanent_country')->nullable();
            $table->string('permanent_zip')->nullable();

            // Parents/Guardian
            $table->string('father_last')->nullable();
            $table->string('father_first')->nullable();
            $table->string('father_middle')->nullable();
            $table->string('father_contact')->nullable();

            $table->string('mother_last')->nullable();
            $table->string('mother_first')->nullable();
            $table->string('mother_middle')->nullable();
            $table->string('mother_contact')->nullable();

            $table->string('guardian_last')->nullable();
            $table->string('guardian_first')->nullable();
            $table->string('guardian_middle')->nullable();
            $table->string('guardian_contact')->nullable();

            $table->json('modality')->nullable();
            $table->json('documents')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
