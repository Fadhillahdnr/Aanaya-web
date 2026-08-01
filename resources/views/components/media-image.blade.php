@props([
    'src',
    'alt' => '',
    'width' => 800,
    'height' => null,
    'crop' => 'limit',
    'sizes' => '100vw',
    'priority' => false,
])

@php
    $ratio = $height ? $width / $height : null;
    $widths = array_values(array_unique(array_filter([320, 480, 720, 960, 1280], fn ($candidate) => $candidate <= max($width, 480))));
    if (! in_array($width, $widths, true)) $widths[] = $width;
@endphp

<img
    src="{{ \App\Support\MediaUrl::image($src, $width, $height, $crop) }}"
    srcset="{{ \App\Support\MediaUrl::srcset($src, $widths, $ratio, $crop) }}"
    sizes="{{ $sizes }}"
    alt="{{ $alt }}"
    width="{{ $width }}"
    @if($height) height="{{ $height }}" @endif
    @if($height) style="aspect-ratio: {{ $width }} / {{ $height }};" @endif
    loading="{{ $priority ? 'eager' : 'lazy' }}"
    decoding="async"
    @if($priority) fetchpriority="high" @endif
    {{ $attributes }}>
