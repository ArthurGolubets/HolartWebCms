<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('t_products')->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->json('attributes')->nullable(); // e.g. {"color": "red", "size": "L"}
            $table->timestamps();

            $table->index('product_id');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_product_variants');
    }
};
