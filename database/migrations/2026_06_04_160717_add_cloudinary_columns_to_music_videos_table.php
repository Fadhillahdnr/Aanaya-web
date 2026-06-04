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
        Schema::table('music_videos', function ($table) {

            $table->text('video_public_id')
                ->nullable();

            $table->text('thumbnail_public_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('music_videos', function (Blueprint $table) {
            //
        });
    }
};
