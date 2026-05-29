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
        'video_file',
        'description',
        'is_featured',

    ];
}