<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MusicVideo extends Model
{
    protected $table = 'music_videos';
    
    protected $fillable = [

        'title',
        'artist',
        'thumbnail',
        'thumbnail_public_id',
        'video_file',
        'video_public_id',
        'description',
        'is_featured',

    ];
}