<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [

        'name',
        'slug',
        'image',
        'image_public_id',
        'description',
        'price',
        'stock',
        'category',
        'is_active'

    ];
}