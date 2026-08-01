<?php

namespace App\Support;

final class MediaUrl
{
    public static function image(?string $url, int $width, ?int $height = null, string $crop = 'limit'): string
    {
        if (! $url || ! str_contains($url, 'res.cloudinary.com') || ! str_contains($url, '/image/upload/')) {
            return (string) $url;
        }

        $transformations = ['f_auto', 'q_auto:eco', 'dpr_auto', 'w_'.$width, 'c_'.$crop];
        if ($height) {
            $transformations[] = 'h_'.$height;
        }

        return str_replace('/image/upload/', '/image/upload/'.implode(',', $transformations).'/', $url);
    }

    public static function srcset(?string $url, array $widths, ?float $ratio = null, string $crop = 'limit'): string
    {
        return collect($widths)
            ->map(fn (int $width) => self::image($url, $width, $ratio ? (int) round($width / $ratio) : null, $crop).' '.$width.'w')
            ->implode(', ');
    }

    public static function video(?string $url, int $width = 1280): string
    {
        if (! $url || ! str_contains($url, 'res.cloudinary.com') || ! str_contains($url, '/video/upload/')) {
            return (string) $url;
        }

        return str_replace('/video/upload/', "/video/upload/f_auto,q_auto:eco,w_{$width},c_limit/", $url);
    }
}
