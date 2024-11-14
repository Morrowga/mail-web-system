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
            $table->integer('uid')->nullable();
            $table->string('message_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('sender')->nullable();
            $table->string('name')->nullable();
            $table->longText('body')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->string('previous_status')->nullable();
            $table->enum('status', ['new', 'read', 'resolved', 'pending', 'replying', 'confirmed'])->default('new');

            $table->timestamps();
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
