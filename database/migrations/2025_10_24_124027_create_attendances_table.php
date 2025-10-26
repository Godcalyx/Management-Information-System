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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade'); 
            $table->string('school_year');
            $table->string('month'); // e.g. "October" or "10"
            $table->integer('days_of_school')->default(0);
            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->integer('times_tardy')->default(0);
            $table->foreignId('encoded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
