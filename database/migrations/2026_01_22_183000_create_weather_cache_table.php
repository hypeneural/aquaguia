<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Create weather_cache table for storing OpenWeather API responses
     * with smart TTL-based caching (6h default)
     */
    public function up(): void
    {
        Schema::create('weather_cache', function (Blueprint $table) {
            $table->id();

            $table->string('provider', 30)->default('openweather');
            $table->string('kind', 20)->default('forecast5'); // forecast5 | current
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->string('units', 16)->default('metric');
            $table->string('lang', 16)->default('pt_br');

            $table->timestamp('fetched_at')->nullable();
            $table->timestamp('next_refresh_at')->nullable();
            $table->unsignedSmallInteger('ttl_hours')->default(6);

            $table->json('payload');
            $table->string('status', 24)->default('ok');
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'kind', 'lat', 'lon', 'units', 'lang'], 'uniq_weather_cache');
            $table->index(['lat', 'lon', 'next_refresh_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_cache');
    }
};
