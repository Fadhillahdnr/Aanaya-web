<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        'title',
        'slug',

        'category',

        'thumbnail',
        'thumbnail_public_id',

        'content',

        'description',

        'author_id',

        'published_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected $casts = [

        'published_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comicImages()
    {
        return $this->hasMany(ComicImage::class)
                    ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isComic()
    {
        return $this->category === 'comic';
    }

    public function isArticle()
    {
        return $this->category === 'article';
    }
}