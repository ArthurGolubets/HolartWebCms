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
        Schema::create('t_filter_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('filter_id')->constrained('t_filters')->onDelete('cascade');
            $table->string('value'); // Значение (например, "512 ГБ", "1 ТБ", "HDD", "SSD")
            $table->string('code')->nullable(); // Символьный код значения (для range типа может быть min/max)
            $table->integer('sort')->default(500);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['filter_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_filter_values');
    }
};
