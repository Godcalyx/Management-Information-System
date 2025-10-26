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
    Schema::table('announcements', function (Blueprint $table) {
    $table->json('target_grade_levels')->nullable(); // e.g. [7,8]
});

}

public function down()
{
    Schema::table('announcements', function (Blueprint $table) {
        $table->dropColumn('grade_level');
    });
}

};
