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
        Schema::create('t_product_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('t_products')->onDelete('cascade');
            $table->foreignId('filter_id')->constrained('t_filters')->onDelete('cascade');
            $table->foreignId('filter_value_id')->constrained('t_filter_values')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_id', 'filter_value_id'], 'product_filter_value_unique');
            $table->index(['product_id', 'filter_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_product_filter_values');
    }
};
