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
        Schema::create('t_comments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('comment');
            $table->tinyInteger('rating')->default(0); // 0-5
            $table->unsignedBigInteger('product_id')->nullable();
            $table->boolean('is_moderated')->default(false);
            $table->timestamps();

            $table->index('product_id');
            $table->index('is_moderated');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_comments');
    }
};
