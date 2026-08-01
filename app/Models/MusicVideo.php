<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasMedia;

class MusicVideo extends Model
{
    use HasMedia;
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
