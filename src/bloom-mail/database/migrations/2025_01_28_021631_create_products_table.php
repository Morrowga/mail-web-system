<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('shop_name');
            $table->date('treatment_begin_date');
            $table->longText('product_detail')->nullable();
            $table->double('price', 10, 2)->nullable();
            $table->date('sale_start_date');
            $table->date('sale_end_date');
            $table->enum('status', ['draft', 'release', 'before_release'])->default('before_release');
            $table->string('purchase_no')->default('0000000000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
