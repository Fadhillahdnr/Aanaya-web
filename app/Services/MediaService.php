<?php

namespace App\Services;

use App\Jobs\DeleteCloudinaryAsset;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MediaService
{
    public function fromRequest(Request $request, string $key, bool $required = false): ?Media
    {
        $id = data_get($request->input('uploaded_media', []), $key);

        if (! $id) {
            if ($required) {
                throw ValidationException::withMessages([$key => 'Upload media belum selesai.']);
            }
            return null;
        }

        return $this->readyOwnedMedia((string) $id);
    }

    public function readyOwnedMedia(string $id): Media
    {
        return Media::query()
            ->whereKey($id)
            ->where('uploaded_by', auth()->id())
            ->where('status', 'ready')
            ->whereNull('mediable_id')
            ->firstOr(function () {
                throw ValidationException::withMessages([
                    'media' => 'Media tidak valid, belum selesai, atau sudah digunakan.',
                ]);
            });
    }

    public function claim(Media $media, Model $model, ?string $purpose = null, int $sortOrder = 0): Media
    {
        if ($purpose !== null && $media->purpose !== $purpose) {
            throw ValidationException::withMessages([
                'media' => "Media {$media->id} tidak sesuai untuk {$purpose}.",
            ]);
        }

        if ($purpose && in_array($purpose, [
            'image', 'cover_image', 'audio_file', 'thumbnail', 'video_file', 'profile_photo',
        ], true)) {
            Media::query()
                ->where('mediable_type', $model->getMorphClass())
                ->where('mediable_id', $model->getKey())
                ->where('purpose', $purpose)
                ->where($media->getKeyName(), '!=', $media->getKey())
                ->delete();
        }

        $media->forceFill([
            'mediable_type' => $model->getMorphClass(),
            'mediable_id' => $model->getKey(),
            'sort_order' => $sortOrder,
        ])->save();

        return $media;
    }

    public function queueDelete(?string $publicId, string $resourceType = 'image'): void
    {
        if ($publicId) {
            DeleteCloudinaryAsset::dispatch($publicId, $resourceType)->afterCommit();
        }
    }
}
