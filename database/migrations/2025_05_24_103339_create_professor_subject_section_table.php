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
    Schema::create('professor_subject_section', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('professor_id');
        $table->unsignedBigInteger('subject_id');
        $table->unsignedBigInteger('section_id');
        $table->timestamps();

        $table->foreign('professor_id')->references('id')->on('professors')->onDelete('cascade');
        $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');

        $table->unique(['professor_id', 'subject_id', 'section_id'], 'prof_sub_sec_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_subject_section');
    }
};
