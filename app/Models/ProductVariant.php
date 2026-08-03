<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasMedia;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'image',
        'image_public_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getEffectivePriceAttribute(): string
    {
        return $this->price ?? $this->product->price;
    }

    public function getDisplayImageAttribute(): ?string
    {
        return $this->image ?: $this->product->image;
    }
}
