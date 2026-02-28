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
        Schema::create('t_info_block_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('info_block_id')->constrained('t_info_blocks')->onDelete('cascade');
            $table->string('code')->comment('Символьный код поля');
            $table->string('name')->comment('Название поля');
            $table->enum('type', ['string', 'text', 'number', 'double', 'bool', 'date', 'datetime', 'image', 'file', 'entity', 'user'])->comment('Тип поля');
            $table->integer('sort')->default(500)->comment('Сортировка');
            $table->boolean('is_required')->default(false)->comment('Обязательное поле');
            $table->boolean('is_multiple')->default(false)->comment('Множественное значение');
            $table->json('settings')->nullable()->comment('Дополнительные настройки поля');
            $table->timestamps();

            $table->index(['info_block_id', 'code']);
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_info_block_fields');
    }
};
