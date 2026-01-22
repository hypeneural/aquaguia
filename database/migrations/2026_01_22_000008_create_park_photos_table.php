<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('park_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('park_id');
            $table->string('url');
            $table->string('caption')->nullable();
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->index('park_id', 'idx_park');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('park_photos');
    }
};
