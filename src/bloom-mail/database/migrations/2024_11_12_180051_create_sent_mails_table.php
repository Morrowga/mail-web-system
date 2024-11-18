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
        Schema::create('sent_mails', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->nullable();
            $table->string('message_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('sender')->nullable();
            $table->string('name')->nullable();
            $table->longText('body')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->foreignId('parent_id')
            ->nullable()
            ->references('id')
            ->on('mail_logs')->onDelete('cascade');
            $table->foreignId('template_id')
            ->nullable()
            ->references('id')
            ->on('templates')->onDelete('cascade');
            $table->string('cc')->nullable();
            $table->string('mailto')->nullable();
            $table->string('bcc')->nullable();
            $table->enum('type', ['sent', 'reply', 'forward']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_mails');
    }
};
