<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->char('abbr', 2)->unique();
            $table->timestamps();

            $table->index('abbr', 'idx_abbr');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
