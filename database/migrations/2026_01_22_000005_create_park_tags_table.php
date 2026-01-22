<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('park_tags', function (Blueprint $table) {
            $table->uuid('park_id');
            $table->uuid('tag_id');

            $table->primary(['park_id', 'tag_id']);

            $table->foreign('park_id')->references('id')->on('parks')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('park_tags');
    }
};
