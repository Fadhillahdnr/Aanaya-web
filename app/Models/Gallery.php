<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasMedia;

class Gallery extends Model
{
    use HasMedia;
    protected $fillable = [

        'title',
        'image',
        'description'

    ];
}
