<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleBlock extends Model
{
    protected $fillable = [
        'article_id',
        'type',
        'content',
        'image',
        'image_public_id',
        'sort_order',
    ];

    public function article()
    {
        return $this->belongsTo(
            Article::class
        );
    }
}