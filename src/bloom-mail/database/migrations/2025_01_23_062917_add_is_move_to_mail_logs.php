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
        Schema::table('mail_logs', function (Blueprint $table) {
            $table->tinyInteger('is_move')->default(0)->after('is_match');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            $table->dropColumn('is_move');
        });
    }
};
