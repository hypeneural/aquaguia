<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comfort_points', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('park_id');
            $table->enum('type', ['sombra', 'fraldario', 'enfermaria', 'microondas', 'bebedouro', 'alimentacao']);
            $table->string('label', 100);
            $table->decimal('x', 5, 2);
            $table->decimal('y', 5, 2);
            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->index('park_id', 'idx_park');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comfort_points');
    }
};
