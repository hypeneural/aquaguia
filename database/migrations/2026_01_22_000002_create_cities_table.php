<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('state_id');
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('image')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->restrictOnDelete();
            $table->index('state_id', 'idx_state');
            $table->index('slug', 'idx_slug');
            $table->index('featured', 'idx_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
