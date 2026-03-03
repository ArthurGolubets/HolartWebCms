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
        Schema::create('t_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название фильтра (например, "Объем данных", "Тип диска")
            $table->string('code')->unique(); // Символьный код (например, "storage_capacity", "disk_type")
            $table->enum('type', ['select', 'checkbox', 'range'])->default('select'); // Тип фильтра
            $table->foreignId('catalog_id')->nullable()->constrained('t_catalogs')->onDelete('cascade'); // null = глобальный фильтр
            $table->integer('sort')->default(500); // Порядок сортировки
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['catalog_id', 'is_active']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_filters');
    }
};
