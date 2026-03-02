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
        // Add is_container to t_page_block_types if it doesn't exist
        if (Schema::hasTable('t_page_block_types') && !Schema::hasColumn('t_page_block_types', 'is_container')) {
            Schema::table('t_page_block_types', function (Blueprint $table) {
                $table->boolean('is_container')->default(false)->after('is_system')->comment('Can contain nested blocks');
            });
        }

        // Add parent_block_id and container_column to t_page_blocks if they don't exist
        if (Schema::hasTable('t_page_blocks')) {
            if (!Schema::hasColumn('t_page_blocks', 'parent_block_id')) {
                Schema::table('t_page_blocks', function (Blueprint $table) {
                    $table->foreignId('parent_block_id')->nullable()->after('block_type_id')->constrained('t_page_blocks')->onDelete('cascade')->comment('For nested blocks in containers');
                    $table->index('parent_block_id');
                });
            }

            if (!Schema::hasColumn('t_page_blocks', 'container_column')) {
                Schema::table('t_page_blocks', function (Blueprint $table) {
                    $table->string('container_column')->nullable()->after('parent_block_id')->comment('Column name in container (col1, col2, etc)');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('t_page_blocks')) {
            Schema::table('t_page_blocks', function (Blueprint $table) {
                if (Schema::hasColumn('t_page_blocks', 'container_column')) {
                    $table->dropColumn('container_column');
                }
                if (Schema::hasColumn('t_page_blocks', 'parent_block_id')) {
                    $table->dropForeign(['parent_block_id']);
                    $table->dropIndex(['parent_block_id']);
                    $table->dropColumn('parent_block_id');
                }
            });
        }

        if (Schema::hasTable('t_page_block_types') && Schema::hasColumn('t_page_block_types', 'is_container')) {
            Schema::table('t_page_block_types', function (Blueprint $table) {
                $table->dropColumn('is_container');
            });
        }
    }
};
