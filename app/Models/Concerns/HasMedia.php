<?php

namespace App\Models\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMedia
{
    public static function bootHasMedia(): void
    {
        static::deleting(function ($model) {
            $model->media()->each(fn (Media $media) => $media->delete());
        });
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
