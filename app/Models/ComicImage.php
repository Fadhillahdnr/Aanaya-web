<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComicImage extends Model
{
    use HasFactory;

    protected $fillable = [

        'article_id',
        'public_id',
        'image',
        'sort_order',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
