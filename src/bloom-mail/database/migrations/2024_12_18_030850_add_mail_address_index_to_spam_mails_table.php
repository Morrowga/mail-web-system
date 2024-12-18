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
        Schema::table('spam_mails', function (Blueprint $table) {
            $table->index('mail_address'); // Create an index on the mail_address column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spam_mails', function (Blueprint $table) {
            $table->dropIndex(['mail_address']); // Drop the index on the mail_address column
        });
    }
};
