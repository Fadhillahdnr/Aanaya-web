<?php

return [
    'image_max_bytes' => (int) env('MEDIA_IMAGE_MAX_MB', 5) * 1024 * 1024,
    'audio_max_bytes' => (int) env('MEDIA_AUDIO_MAX_MB', 20) * 1024 * 1024,
    'video_max_bytes' => (int) env('MEDIA_VIDEO_MAX_MB', 150) * 1024 * 1024,
    'video_max_duration' => (int) env('MEDIA_VIDEO_MAX_DURATION_SECONDS', 180),
    'video_max_long_edge' => (int) env('MEDIA_VIDEO_MAX_LONG_EDGE', 1920),
    'video_max_short_edge' => (int) env('MEDIA_VIDEO_MAX_SHORT_EDGE', 1080),
    'video_chunk_bytes' => (int) env('MEDIA_VIDEO_CHUNK_MB', 10) * 1024 * 1024,
    'stale_after_hours' => (int) env('MEDIA_STALE_AFTER_HOURS', 24),
];
