<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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