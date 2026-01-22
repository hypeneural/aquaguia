<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Fix media table model_id column for UUID compatibility
     * This migration alters the existing column type from BIGINT to CHAR(36)
     */
    public function up(): void
    {
        // Check if media table exists and needs modification
        if (Schema::hasTable('media')) {
            // Get the current column type
            $columnType = DB::selectOne("SHOW COLUMNS FROM media WHERE Field = 'model_id'")->Type ?? '';

            // Only modify if it's still bigint (not already UUID compatible)
            if (str_contains(strtolower($columnType), 'bigint')) {
                // Drop existing indexes
                Schema::table('media', function (Blueprint $table) {
                    $table->dropIndex(['model_type', 'model_id']);
                });

                // Modify the column type to support UUIDs
                DB::statement('ALTER TABLE media MODIFY model_id CHAR(36) NOT NULL');

                // Recreate the index
                Schema::table('media', function (Blueprint $table) {
                    $table->index(['model_type', 'model_id']);
                });
            }
        }
    }

    public function down(): void
    {
        // Revert back to bigint (will lose UUID data)
        if (Schema::hasTable('media')) {
            Schema::table('media', function (Blueprint $table) {
                $table->dropIndex(['model_type', 'model_id']);
            });

            DB::statement('ALTER TABLE media MODIFY model_id BIGINT UNSIGNED NOT NULL');

            Schema::table('media', function (Blueprint $table) {
                $table->index(['model_type', 'model_id']);
            });
        }
    }
};
