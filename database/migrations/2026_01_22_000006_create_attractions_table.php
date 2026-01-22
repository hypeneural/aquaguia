<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attractions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('park_id');
            $table->string('name', 100);
            $table->enum('type', ['infantil', 'família', 'radical']);
            $table->unsignedSmallInteger('min_height_cm')->default(0);
            $table->unsignedSmallInteger('max_height_cm')->nullable();
            $table->unsignedTinyInteger('adrenaline')->default(0);
            $table->unsignedSmallInteger('avg_queue_minutes')->default(0);
            $table->text('description')->nullable();
            $table->boolean('has_double_float')->default(false);
            $table->string('image')->nullable();
            $table->boolean('is_open')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->index('park_id', 'idx_park');
            $table->index('type', 'idx_type');
            $table->index(['min_height_cm', 'max_height_cm'], 'idx_height');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
