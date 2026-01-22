<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Create park_operating_hours table for detailed schedule by day of week
     * And add accessibility_features to parks
     */
    public function up(): void
    {
        // Create operating hours table
        Schema::create('park_operating_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('park_id');

            $table->enum('day_of_week', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);

            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);

            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->unique(['park_id', 'day_of_week']);
        });

        // Create special hours table (holidays, seasons)
        Schema::create('park_special_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('park_id');

            $table->string('name', 100); // "Temporada de Verão", "Feriado de Natal"
            $table->date('start_date');
            $table->date('end_date');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->string('closure_reason', 200)->nullable();

            $table->timestamps();

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->index(['park_id', 'start_date', 'end_date']);
        });

        // Add accessibility to parks
        Schema::table('parks', function (Blueprint $table) {
            $table->json('accessibility_features')->nullable()->after('anti_queue_tips');
            $table->json('payment_methods')->nullable()->after('accessibility_features');
            $table->json('languages_spoken')->nullable()->after('payment_methods');
            $table->json('food_options')->nullable()->after('languages_spoken');
            $table->json('prohibited_items')->nullable()->after('food_options');
            $table->json('what_to_bring')->nullable()->after('prohibited_items');
            $table->unsignedSmallInteger('minimum_stay_hours')->nullable()->after('what_to_bring');
            $table->boolean('reservation_required')->default(false)->after('minimum_stay_hours');
            $table->unsignedInteger('max_capacity')->nullable()->after('reservation_required');
            $table->string('arrival_recommendation', 100)->nullable()->after('max_capacity');
        });

        // Add fields to park_photos
        Schema::table('park_photos', function (Blueprint $table) {
            $table->string('thumbnail_url')->nullable()->after('url');
            $table->unsignedSmallInteger('width')->nullable()->after('caption');
            $table->unsignedSmallInteger('height')->nullable()->after('width');
            $table->boolean('is_hero')->default(false)->after('height');
        });

        // Add fields to park_videos
        Schema::table('park_videos', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_seconds')->nullable()->after('youtube_id');
            $table->string('thumbnail_url')->nullable()->after('duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('park_special_hours');
        Schema::dropIfExists('park_operating_hours');

        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn([
                'accessibility_features',
                'payment_methods',
                'languages_spoken',
                'food_options',
                'prohibited_items',
                'what_to_bring',
                'minimum_stay_hours',
                'reservation_required',
                'max_capacity',
                'arrival_recommendation',
            ]);
        });

        Schema::table('park_photos', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_url', 'width', 'height', 'is_hero']);
        });

        Schema::table('park_videos', function (Blueprint $table) {
            $table->dropColumn(['duration_seconds', 'thumbnail_url']);
        });
    }
};
