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
        Schema::create('t_info_block_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('info_block_id')->constrained('t_info_blocks')->onDelete('cascade');
            $table->string('name')->comment('Название элемента');
            $table->string('code')->nullable()->comment('Символьный код элемента');
            $table->boolean('is_active')->default(true)->comment('Активность элемента');
            $table->integer('sort')->default(500)->comment('Сортировка');
            $table->json('properties')->comment('Значения свойств в JSON');
            $table->timestamps();

            $table->index(['info_block_id', 'is_active']);
            $table->index('code');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_info_block_elements');
    }
};
