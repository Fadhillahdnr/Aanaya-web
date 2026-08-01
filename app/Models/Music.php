<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\InvalidatesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasMedia, InvalidatesPublicContentCache;
    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    protected $table = 'musics';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'title',
        'artist',
        'slug',
        'cover_image',
        'cover_public_id',
        'audio_file',
        'audio_public_id',
        'spotify_link',
        'youtube_link',
        'description',
        'release_date',

    ];
}
