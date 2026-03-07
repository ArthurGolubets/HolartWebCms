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
            $table->string('title');
            $table->string('header_template')->nullable() ;// TODO: Убрать!
            $table->string('footer_template')->nullable() ;// TODO: Убрать!
            $table->string('slug')->unique();
            $table->enum('type', ['static', 'dynamic'])->default('static');
            $table->longText('content')->nullable()->comment('For static pages');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort')->default(500);
            $table->timestamps();

            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
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
