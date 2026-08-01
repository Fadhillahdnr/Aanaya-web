<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\InvalidatesPublicContentCache;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasMedia, InvalidatesPublicContentCache;

    protected $fillable = [

        'name',
        'slug',
        'image',
        'image_public_id',
        'description',
        'price',
        'stock',
        'category',
        'is_active',

    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
