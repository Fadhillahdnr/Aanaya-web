<?php

namespace App\Jobs;

use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteCloudinaryAsset implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(
        public string $publicId,
        public string $resourceType = 'image',
    ) {}

    public function handle(Cloudinary $cloudinary): void
    {
        $cloudinary->uploadApi()->destroy($this->publicId, [
            'resource_type' => $this->resourceType,
            'invalidate' => true,
        ]);
    }
}
