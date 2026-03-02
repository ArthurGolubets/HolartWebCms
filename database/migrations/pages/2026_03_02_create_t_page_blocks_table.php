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
        Schema::create('t_page_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained('t_pages')->onDelete('cascade');
            $table->foreignId('block_type_id')->constrained('t_page_block_types')->onDelete('restrict');
            $table->foreignId('parent_block_id')->nullable()->constrained('t_page_blocks')->onDelete('cascade')->comment('For nested blocks in containers');
            $table->string('container_column')->nullable()->comment('Column name in container (col1, col2, etc)');
            $table->json('settings')->nullable()->comment('Block-specific settings');
            $table->integer('sort')->default(500);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('page_id');
            $table->index('block_type_id');
            $table->index('parent_block_id');
            $table->index('sort');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_page_blocks');
    }
};
