<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;

trait InvalidatesPublicContentCache
{
    public static function bootInvalidatesPublicContentCache(): void
    {
        static::saved(fn () => static::forgetPublicContentCache());
        static::deleted(fn () => static::forgetPublicContentCache());
    }

    private static function forgetPublicContentCache(): void
    {
        Cache::forget('public.dashboard.v2');
        Cache::forget('public.music.v2');
        Cache::forget('public.music.v3');
    }
}
