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
        Schema::create('t_page_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->nullable()->constrained('t_pages')->onDelete('cascade');
            $table->string('url'); // Полный URL посещенной страницы
            $table->string('route_name')->nullable(); // Имя роута
            $table->string('ip_address', 45)->nullable(); // IP адрес (IPv4 и IPv6)
            $table->text('user_agent')->nullable(); // User Agent
            $table->string('referer')->nullable(); // Referer URL
            $table->timestamp('visited_at'); // Дата и время посещения

            // Индексы для оптимизации запросов статистики
            $table->index('page_id');
            $table->index('url');
            $table->index('route_name');
            $table->index('visited_at');
            $table->index(['page_id', 'visited_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_page_visits');
    }
};
