<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('t_catalogs')->onDelete('cascade');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('keywords')->nullable();
            $table->string('image')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_catalogs');
    }
};
