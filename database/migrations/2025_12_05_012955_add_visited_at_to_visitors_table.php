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
    Schema::table('visitors', function (Blueprint $table) {
        $table->date('visited_at')->default(DB::raw('CURRENT_DATE'))->after('ip_address');
    });
}

public function down(): void
{
    Schema::table('visitors', function (Blueprint $table) {
        $table->dropColumn('visited_at');
    });
}

};
