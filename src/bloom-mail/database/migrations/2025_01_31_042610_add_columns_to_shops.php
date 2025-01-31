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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('shop_type')->nullable();
            $table->string('address')->nullable();
            $table->integer('room_numbers')->default(0);
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->string('phone_no')->nullable();
            $table->time('reception_start_time')->nullable();
            $table->time('reception_end_time')->nullable();
            $table->string('close_day')->nullable();
            $table->string('close_day_text')->nullable();
            $table->string('access')->nullable();
            $table->string('parking_nearby')->nullable();
            $table->string('store_direction')->nullable();
            $table->longText('gmap_location')->nullable();
            $table->longText('gmap_photos')->nullable();
            $table->longText('youtube')->nullable();
            $table->string('top_statement')->nullable();
            $table->string('store_sub_title')->nullable();
            $table->string('store_btm_text')->nullable();
            $table->string('store_sub_title_two')->nullable();
            $table->string('store_btm_text_two')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('shop_type');
            $table->dropColumn('address');
            $table->dropColumn('opening_time');
            $table->dropColumn('closing_time');
            $table->dropColumn('phone_no');
            $table->dropColumn('reception_start_time');
            $table->dropColumn('reception_end_time');
            $table->dropColumn('close_day');
            $table->dropColumn('close_day_text');
            $table->dropColumn('access');
            $table->dropColumn('parking_nearby');
            $table->dropColumn('store_direction');
            $table->dropColumn('gmap_location');
            $table->dropColumn('gmap_photos');
            $table->dropColumn('youtube');
            $table->dropColumn('top_statement');
            $table->dropColumn('store_sub_title');
            $table->dropColumn('store_btm_text');
            $table->dropColumn('store_sub_title_two');
            $table->dropColumn('store_btm_text_two');
        });
    }
};
