<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use App\Models\Concerns\InvalidatesPublicContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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
        'variant_label',
        'is_active',

    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function galleryMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->whereIn('purpose', ['image', 'product_images'])
            ->orderBy('sort_order');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function getHasVariantsAttribute(): bool
    {
        if (! $this->variant_label) {
            return false;
        }

        return $this->relationLoaded('activeVariants')
            ? $this->activeVariants->isNotEmpty()
            : $this->activeVariants()->exists();
    }

    public function getGalleryImagesAttribute()
    {
        $media = $this->relationLoaded('galleryMedia')
            ? $this->galleryMedia
            : $this->galleryMedia()->get();

        return collect([$this->image])
            ->merge($media->where('media_type', 'image')->pluck('secure_url'))
            ->filter()
            ->unique()
            ->values();
    }

    public function getGalleryItemsAttribute()
    {
        $media = $this->relationLoaded('galleryMedia')
            ? $this->galleryMedia
            : $this->galleryMedia()->get();

        $items = $media->map(fn ($item) => [
            'type' => $item->media_type === 'video' ? 'video' : 'image',
            'url' => $item->secure_url,
            'thumbnail' => $item->thumbnail_url ?: $item->secure_url,
        ])->filter(fn ($item) => $item['url']);

        if ($this->image && ! $items->contains(fn ($item) => $item['url'] === $this->image)) {
            $items->prepend([
                'type' => 'image',
                'url' => $this->image,
                'thumbnail' => $this->image,
            ]);
        }

        return $items->unique(fn ($item) => $item['type'].'|'.$item['url'])->values();
    }
}
