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
        Schema::create('t_dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('t_administrators')->onDelete('cascade');
            $table->string('widget_type', 50); // 'users_stats', 'popular_pages', 'recent_logs', 'orders_stats', etc.
            $table->integer('position')->default(0); // Позиция виджета на дашборде
            $table->json('settings')->nullable(); // Дополнительные настройки виджета
            $table->boolean('is_visible')->default(true);
            $table->integer('width')->default(6); // 1-12 (bootstrap grid)
            $table->timestamps();

            $table->index(['admin_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_dashboard_widgets');
    }
};
