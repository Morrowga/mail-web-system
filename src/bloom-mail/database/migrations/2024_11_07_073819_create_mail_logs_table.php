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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->unique();
            $table->string('subject')->nullable();
            $table->string('sender')->nullable();
            $table->string('name')->nullable();
            $table->text('body')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('mail_logs')->onDelete('cascade');
            $table->enum('status', ['new', 'read','confirming','pending', 'replying', 'confirmed', 'corresponding'])->default('new');
            $table->timestamps();
            $table->timestamp('datetime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
