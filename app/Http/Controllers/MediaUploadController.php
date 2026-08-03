<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Cloudinary\Api\ApiUtils;
use Cloudinary\Cloudinary;
use Cloudinary\Utils\SignatureVerifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaUploadController extends Controller
{
    private const PURPOSES = [
        'image', 'cover_image', 'audio_file', 'thumbnail', 'video_file',
        'profile_photo', 'comic_images', 'article_block_image',
        'product_images',
        'product_variant_image',
    ];

    public function sign(Request $request, Cloudinary $cloudinary): JsonResponse
    {
        $validated = $request->validate([
            'purpose' => ['required', Rule::in(self::PURPOSES)],
            'original_name' => ['required', 'string', 'max:255'],
            'mime_type' => ['required', 'string', 'max:100'],
            'size_bytes' => ['required', 'integer', 'min:1'],
            'media_type' => ['required', Rule::in(['image', 'audio', 'video'])],
        ]);

        $this->authorizePurpose($request, $validated['purpose']);
        $this->validateFilePolicy($validated['media_type'], $validated['mime_type'], $validated['size_bytes']);

        $resourceType = $validated['media_type'] === 'image' ? 'image' : 'video';
        $environment = app()->environment('production') ? 'production' : 'development';
        $folder = "{$environment}/{$this->folderFor($validated['purpose'])}";
        $publicId = (string) Str::ulid();

        $parameters = [
            'folder' => $folder,
            'public_id' => $publicId,
            'timestamp' => now()->timestamp,
            'overwrite' => 'false',
        ];
        ApiUtils::signRequest($parameters, $cloudinary->configuration->cloud);

        $media = Media::create([
            'uploaded_by' => $request->user()->id,
            'public_id' => "{$folder}/{$publicId}",
            'resource_type' => $resourceType,
            'media_type' => $validated['media_type'],
            'purpose' => $validated['purpose'],
            'original_name' => basename($validated['original_name']),
            'mime_type' => $validated['mime_type'],
            'size_bytes' => $validated['size_bytes'],
            'status' => 'pending',
            'metadata' => ['upload_requested_at' => now()->toIso8601String()],
        ]);

        return response()->json([
            'media_id' => $media->id,
            'upload_url' => "https://api.cloudinary.com/v1_1/{$cloudinary->configuration->cloud->cloudName}/{$resourceType}/upload",
            'parameters' => $parameters,
        ]);
    }

    public function complete(Request $request, Media $media, Cloudinary $cloudinary): JsonResponse
    {
        abort_unless($media->uploaded_by === $request->user()->id, 403);
        abort_unless($media->status === 'pending', 409, 'Upload sudah diselesaikan.');

        $validated = $request->validate([
            'public_id' => ['required', 'string', 'max:255'],
            'version' => ['required', 'integer'],
            'signature' => ['required', 'string'],
            'secure_url' => ['required', 'url', 'max:2048'],
            'resource_type' => ['required', Rule::in(['image', 'video', 'raw'])],
            'format' => ['nullable', 'string', 'max:20'],
            'bytes' => ['required', 'integer', 'min:1'],
            // Cloudinary returns width=0 and height=0 for audio resources.
            // Video/image dimensions are checked separately when relevant.
            'width' => ['nullable', 'integer', 'min:0'],
            'height' => ['nullable', 'integer', 'min:0'],
            'duration' => ['nullable', 'numeric', 'min:0'],
        ]);

        abort_unless(hash_equals($media->public_id, $validated['public_id']), 422, 'Public ID upload tidak cocok.');
        abort_unless($media->resource_type === $validated['resource_type'], 422, 'Resource type tidak cocok.');
        abort_unless(SignatureVerifier::verifyApiResponseSignature(
            $validated['public_id'],
            $validated['version'],
            $validated['signature'],
        ), 422, 'Signature respons Cloudinary tidak valid.');

        $this->validateFilePolicy($media->media_type, $media->mime_type, $validated['bytes']);
        if ($media->media_type === 'video') {
            abort_if(($validated['duration'] ?? 0) > config('media.video_max_duration'), 422, 'Durasi video melebihi batas aplikasi.');
            $width = (int) ($validated['width'] ?? 0);
            $height = (int) ($validated['height'] ?? 0);
            abort_if(
                max($width, $height) > config('media.video_max_long_edge') || min($width, $height) > config('media.video_max_short_edge'),
                422,
                'Resolusi video melebihi batas aplikasi.',
            );
        }

        $format = $validated['format'] ?? null;
        $extension = $format ? '.'.$format : '';
        $baseUrl = "https://res.cloudinary.com/{$cloudinary->configuration->cloud->cloudName}/{$validated['resource_type']}/upload";
        $secureUrl = "{$baseUrl}/v{$validated['version']}/{$validated['public_id']}{$extension}";
        $thumbnailUrl = $media->media_type === 'image'
            ? "{$baseUrl}/c_fill,w_400,h_400,q_auto,f_auto/v{$validated['version']}/{$validated['public_id']}{$extension}"
            : "{$baseUrl}/so_1,c_fill,w_640,h_360,q_auto,f_jpg/v{$validated['version']}/{$validated['public_id']}.jpg";

        $media->update([
            'format' => $validated['format'] ?? null,
            'size_bytes' => $validated['bytes'],
            'width' => $validated['width'] ?? null,
            'height' => $validated['height'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'secure_url' => $secureUrl,
            'thumbnail_url' => $thumbnailUrl,
            'status' => 'ready',
            'uploaded_at' => now(),
            'processed_at' => now(),
            'metadata' => ['cloudinary_version' => $validated['version']],
        ]);

        return response()->json(['media' => $media->fresh()]);
    }

    private function authorizePurpose(Request $request, string $purpose): void
    {
        if ($purpose !== 'profile_photo') {
            abort_unless($request->user()->isAdmin(), 403);
        }
    }

    private function validateFilePolicy(string $mediaType, string $mimeType, int $bytes): void
    {
        $allowed = match ($mediaType) {
            'image' => ['image/jpeg', 'image/png', 'image/webp'],
            'audio' => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/x-wav', 'audio/mp4', 'audio/x-m4a'],
            'video' => ['video/mp4', 'video/webm'],
        };

        abort_unless(in_array(strtolower($mimeType), $allowed, true), 422, 'Tipe file tidak didukung.');
        abort_if($bytes > config("media.{$mediaType}_max_bytes"), 422, 'Ukuran file melebihi batas aplikasi.');
    }

    private function folderFor(string $purpose): string
    {
        return match ($purpose) {
            'profile_photo' => 'users/avatars',
            'cover_image' => 'music/covers',
            'audio_file' => 'music/audio',
            'video_file' => 'music-videos/videos',
            'thumbnail' => 'articles-and-videos/thumbnails',
            'comic_images' => 'articles/comics',
            'article_block_image' => 'articles/blocks',
            'product_images' => 'products/images',
            'product_variant_image' => 'products/variants',
            default => 'products/images',
        };
    }
}
