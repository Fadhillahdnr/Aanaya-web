<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | NEW CATEGORY
            |--------------------------------------------------------------------------
            */

            $table->string('category')
                ->default('article')
                ->after('slug');

            /*
            |--------------------------------------------------------------------------
            | COMIC DESCRIPTION
            |--------------------------------------------------------------------------
            */

            $table->longText('description')
                ->nullable()
                ->after('content');

            /*
            |--------------------------------------------------------------------------
            | CONTENT NULLABLE
            |--------------------------------------------------------------------------
            */

            $table->longText('content')
                ->nullable()
                ->change();

            /*
            |--------------------------------------------------------------------------
            | THUMBNAIL NULLABLE
            |--------------------------------------------------------------------------
            */

            $table->string('thumbnail')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {

            $table->dropColumn('category');

            $table->dropColumn('description');
        });
    }
};