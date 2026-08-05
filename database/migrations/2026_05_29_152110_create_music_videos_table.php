<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_videos', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('artist')
                ->default('Aanaya');

            $table->string('thumbnail')->nullable();

            $table->string('video_file');

            $table->text('description')->nullable();

            $table->boolean('is_featured')
                ->default(false);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_videos');
    }
};
