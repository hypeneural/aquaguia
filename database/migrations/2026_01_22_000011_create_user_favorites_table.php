<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->uuid('park_id');
            $table->timestamps();

            $table->primary(['user_id', 'park_id']);
            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorites');
    }
};
