<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('musics', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->string('artist')
                ->default('Aanaya');

            $table->string('slug')->unique();

            $table->string('cover_image');

            $table->string('audio_file');

            $table->text('spotify_link')->nullable();

            $table->text('youtube_link')->nullable();

            $table->text('description')->nullable();

            $table->date('release_date')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('music');
    }
};
