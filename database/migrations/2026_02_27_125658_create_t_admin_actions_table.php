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
        Schema::create('t_admin_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('t_administrators')->onDelete('cascade');
            $table->string('action'); // create, update, delete, login, etc.
            $table->string('entity_type')->nullable(); // catalog, product, administrator, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('data')->nullable(); // Additional data
            $table->timestamps();

            $table->index(['admin_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_admin_actions');
    }
};
