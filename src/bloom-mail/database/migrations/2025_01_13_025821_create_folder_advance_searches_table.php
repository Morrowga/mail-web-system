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
        Schema::create('folder_advance_searches', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_exclude')->default(false);
            $table->text('search_character');
            $table->enum('method', ['exact_match', 'partial_match', 'front_match', 'backward_match']);
            $table->foreignId('folder_id')
            ->nullable()
            ->references('id')
            ->on('folders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_advance_searches');
    }
};
