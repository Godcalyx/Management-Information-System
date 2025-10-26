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
    Schema::create('announcements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained('users')->after('id');
        $table->string('title');
        $table->text('content');
        $table->enum('visibility', ['students', 'professors', 'all'])->default('all');
        $table->timestamp('published_at')->nullable();
        $table->timestamps();
    });
}




    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
