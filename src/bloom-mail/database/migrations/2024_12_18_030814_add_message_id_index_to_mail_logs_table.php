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
            $table->index('message_id'); // Create an index on the message_id column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_logs', function (Blueprint $table) {
            $table->dropIndex(['message_id']); // Drop the index on the message_id column
        });
    }
};
