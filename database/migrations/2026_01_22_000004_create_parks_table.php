<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('city_id');

            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description');
            $table->string('hero_image');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('opening_hours', 50);

            $table->decimal('price_adult', 10, 2);
            $table->decimal('price_child', 10, 2);
            $table->decimal('price_parking', 10, 2)->default(0);
            $table->decimal('price_locker', 10, 2)->default(0);

            $table->unsignedTinyInteger('water_heated_areas')->default(0);
            $table->enum('shade_level', ['baixa', 'média', 'alta']);
            $table->unsignedTinyInteger('family_index')->default(0);

            $table->json('best_for')->nullable();
            $table->json('not_for')->nullable();
            $table->json('anti_queue_tips')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('city_id')->references('id')->on('cities')->restrictOnDelete();

            $table->index('city_id', 'idx_city');
            $table->index('family_index', 'idx_family_index');
            $table->index('price_adult', 'idx_price');
            $table->index('is_active', 'idx_active');
        });

        // Add fulltext index for search (MariaDB/MySQL)
        DB::statement('ALTER TABLE parks ADD FULLTEXT INDEX idx_search (name, description)');
    }

    public function down(): void
    {
        Schema::dropIfExists('parks');
    }
};
