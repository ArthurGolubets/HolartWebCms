<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->decimal('value', 10, 2)->default(0);
            $table->enum('type', ['fiat', 'percent'])->default('percent');
            $table->integer('max_usage')->default(0); // 0 = безлимитное использование
            $table->integer('current_usage')->default(0);
            $table->timestamp('date_active_from')->nullable();
            $table->timestamp('date_active_to')->nullable();
            $table->timestamps();

            $table->index('code');
            $table->index(['date_active_from', 'date_active_to']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_promocodes');
    }
};
