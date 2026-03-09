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
        Schema::create('t_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Название страницы
            $table->string('slug')->unique(); // Уникальный URL slug
            $table->enum('type', ['static', 'dynamic'])->default('static'); // Тип страницы
            $table->string('route_name')->nullable(); // Имя роута (для динамических страниц)
            $table->text('content')->nullable(); // Контент страницы (для статических)
            $table->string('meta_title')->nullable(); // SEO заголовок
            $table->text('meta_description')->nullable(); // SEO описание
            $table->string('meta_keywords')->nullable(); // SEO ключевые слова
            $table->boolean('is_active')->default(true); // Активна/Неактивна
            $table->unsignedBigInteger('views_count')->default(0); // Кэшированное количество просмотров
            $table->timestamps();

            // Индексы для оптимизации
            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
            $table->index('route_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_pages');
    }
};
