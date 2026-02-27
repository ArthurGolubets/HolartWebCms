<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->json('addition_data')->nullable();
            $table->enum('delivery_type', ['pickup', 'courier', 'post'])->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->enum('payment_type', ['online', 'cash'])->default('cash');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->enum('payment_status', ['pending', 'success', 'canceled'])->default('pending');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->decimal('goods_price', 10, 2)->default(0);
            $table->decimal('delivery_price', 10, 2)->default(0);
            $table->unsignedBigInteger('promocode_id')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('phone');
            $table->index('payment_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_orders');
    }
};
