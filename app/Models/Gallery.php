<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\InvalidatesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasMedia, InvalidatesPublicContentCache;

    protected $fillable = [

        'title',
        'image',
        'description',

    ];
}
