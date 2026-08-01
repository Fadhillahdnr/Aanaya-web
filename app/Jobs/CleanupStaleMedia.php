<?php

namespace App\Jobs;

use App\Models\Media;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CleanupStaleMedia implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function handle(Cloudinary $cloudinary): void
    {
        Media::query()
            ->whereNull('mediable_id')
            ->whereIn('status', ['pending', 'ready'])
            ->where('created_at', '<', now()->subHours(config('media.stale_after_hours')))
            ->chunkById(50, function ($mediaItems) use ($cloudinary) {
                foreach ($mediaItems as $media) {
                    $cloudinary->uploadApi()->destroy($media->public_id, [
                        'resource_type' => $media->resource_type,
                        'invalidate' => true,
                    ]);
                    $media->forceDelete();
                }
            });
    }
}
