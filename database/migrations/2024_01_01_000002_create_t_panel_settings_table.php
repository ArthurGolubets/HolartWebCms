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
        Schema::create('t_panel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, array, json
            $table->timestamps();
        });

        // Insert default settings
        DB::table('t_panel_settings')->insert([
            ['key' => 'panel_name', 'value' => 'HolartCMS', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'theme_color', 'value' => 'red', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'company_name', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'phones', 'value' => '[]', 'type' => 'array', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'work_hours', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'addresses', 'value' => '[]', 'type' => 'array', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bitrix24_webhook', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_shop_id', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_secret', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'telegram_chat_id', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'telegram_token', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'header_code', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'footer_code', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_panel_settings');
    }
};
