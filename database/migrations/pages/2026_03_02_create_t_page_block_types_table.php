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
        Schema::create('t_page_block_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable()->comment('Icon class or SVG');
            $table->string('template')->nullable()->comment('Blade template path');
            $table->json('fields_schema')->nullable()->comment('JSON schema for block settings');
            $table->string('preview_image')->nullable()->comment('Preview image URL');
            $table->string('category')->default('general')->comment('hero, content, media, etc');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_container')->default(false)->comment('Can contain nested blocks');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_page_block_types');
    }
};
