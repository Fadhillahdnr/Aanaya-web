<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMusicVideoPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_responsive_direct_upload_music_video_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.music-vidio-create'))
            ->assertOk()
            ->assertSee('data-cloudinary-direct-upload', false)
            ->assertSee('data-mv-thumbnail-preview', false)
            ->assertSee('data-mv-video-preview', false)
            ->assertSee('name="video_file"', false)
            ->assertSee('accept="video/mp4,video/webm"', false)
            ->assertSee('data-video-max-bytes="'.config('media.video_max_bytes').'"', false)
            ->assertSee('up to 150 MB')
            ->assertSee('10 MB parts')
            ->assertSee('Upload Music Video');
    }
}
