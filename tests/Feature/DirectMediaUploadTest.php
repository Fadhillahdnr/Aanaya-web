<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Utils\SignatureVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectMediaUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_signed_direct_upload_intent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/media/uploads/sign', [
            'purpose' => 'image',
            'original_name' => 'product.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 120000,
            'media_type' => 'image',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['media_id', 'upload_url', 'upload_strategy', 'chunk_size_bytes', 'parameters' => ['signature', 'api_key', 'timestamp', 'folder', 'public_id']])
            ->assertJsonPath('upload_strategy', 'single')
            ->assertJsonPath('chunk_size_bytes', null);

        $this->assertDatabaseHas('media', [
            'id' => $response->json('media_id'),
            'uploaded_by' => $admin->id,
            'status' => 'pending',
            'purpose' => 'image',
        ]);
        $this->assertStringNotContainsString((string) Configuration::instance()->cloud->apiSecret, $response->getContent());
    }

    public function test_regular_user_cannot_request_admin_media_upload(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)->postJson('/media/uploads/sign', [
            'purpose' => 'video_file',
            'original_name' => 'video.mp4',
            'mime_type' => 'video/mp4',
            'size_bytes' => 1000,
            'media_type' => 'video',
        ])->assertForbidden();
    }

    public function test_oversized_upload_is_rejected_before_cloudinary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->postJson('/media/uploads/sign', [
            'purpose' => 'video_file',
            'original_name' => 'huge.mp4',
            'mime_type' => 'video/mp4',
            'size_bytes' => config('media.video_max_bytes') + 1,
            'media_type' => 'video',
        ])->assertUnprocessable();

        $this->assertDatabaseCount('media', 0);
    }

    public function test_large_video_receives_a_ten_megabyte_chunked_upload_intent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->postJson('/media/uploads/sign', [
            'purpose' => 'video_file',
            'original_name' => 'music-video.mp4',
            'mime_type' => 'video/mp4',
            'size_bytes' => 100 * 1024 * 1024,
            'media_type' => 'video',
        ])->assertOk()
            ->assertJsonPath('upload_strategy', 'chunked')
            ->assertJsonPath('chunk_size_bytes', 10 * 1024 * 1024);
    }

    public function test_video_above_full_hd_resolution_is_rejected_on_completion(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'public_id' => 'development/music-videos/videos/large-video',
            'resource_type' => 'video',
            'media_type' => 'video',
            'purpose' => 'video_file',
            'original_name' => 'large-video.mp4',
            'mime_type' => 'video/mp4',
            'size_bytes' => 1000,
            'status' => 'pending',
        ]);
        $version = 123456791;
        $signature = SignatureVerifier::generateHmac(
            "public_id={$media->public_id}&version={$version}",
            (string) Configuration::instance()->cloud->apiSecret,
        );

        $this->actingAs($admin)->postJson("/media/uploads/{$media->id}/complete", [
            'public_id' => $media->public_id,
            'version' => $version,
            'signature' => $signature,
            'secure_url' => 'https://res.cloudinary.com/demo/video/upload/v123/large-video.mp4',
            'resource_type' => 'video',
            'format' => 'mp4',
            'bytes' => 1000,
            'width' => 3840,
            'height' => 2160,
            'duration' => 120,
        ])->assertUnprocessable()
            ->assertSeeText('Resolusi video melebihi batas aplikasi.');

        $this->assertSame('pending', $media->fresh()->status);
    }

    public function test_cloudinary_response_signature_is_verified_before_media_becomes_ready(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'public_id' => 'development/products/images/test-id',
            'resource_type' => 'image',
            'media_type' => 'image',
            'purpose' => 'image',
            'original_name' => 'product.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1000,
            'status' => 'pending',
        ]);
        $version = 123456789;
        $signature = SignatureVerifier::generateHmac(
            "public_id={$media->public_id}&version={$version}",
            (string) Configuration::instance()->cloud->apiSecret,
        );

        $this->actingAs($admin)->postJson("/media/uploads/{$media->id}/complete", [
            'public_id' => $media->public_id,
            'version' => $version,
            'signature' => $signature,
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/v123/product.jpg',
            'resource_type' => 'image',
            'format' => 'jpg',
            'bytes' => 1000,
            'width' => 800,
            'height' => 600,
        ])->assertOk()->assertJsonPath('media.status', 'ready');

        $this->assertSame('ready', $media->fresh()->status);
    }

    public function test_audio_upload_accepts_zero_width_and_height_from_cloudinary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'public_id' => 'development/music/audio/test-audio',
            'resource_type' => 'video',
            'media_type' => 'audio',
            'purpose' => 'audio_file',
            'original_name' => 'song.mp3',
            'mime_type' => 'audio/mpeg',
            'size_bytes' => 1000,
            'status' => 'pending',
        ]);
        $version = 123456790;
        $signature = SignatureVerifier::generateHmac(
            "public_id={$media->public_id}&version={$version}",
            (string) Configuration::instance()->cloud->apiSecret,
        );

        $this->actingAs($admin)->postJson("/media/uploads/{$media->id}/complete", [
            'public_id' => $media->public_id,
            'version' => $version,
            'signature' => $signature,
            'secure_url' => 'https://res.cloudinary.com/demo/video/upload/v123/song.mp3',
            'resource_type' => 'video',
            'format' => 'mp3',
            'bytes' => 1000,
            'width' => 0,
            'height' => 0,
            'duration' => 180.5,
        ])->assertOk()->assertJsonPath('media.status', 'ready');

        $media->refresh();
        $this->assertSame(0, $media->width);
        $this->assertSame(0, $media->height);
    }
}
