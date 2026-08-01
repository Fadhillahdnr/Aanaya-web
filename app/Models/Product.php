<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasMedia;

class Product extends Model
{
    use HasMedia;
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
