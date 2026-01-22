<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Make hero_image nullable and add default values
     */
    public function up(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->string('hero_image')->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->string('hero_image')->nullable(false)->change();
        });
    }
};
