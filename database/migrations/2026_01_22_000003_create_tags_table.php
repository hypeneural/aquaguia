<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug', 50)->unique();
            $table->string('label', 50);
            $table->string('emoji', 10)->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();

            $table->index('slug', 'idx_slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
