<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMusicUploadPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_responsive_direct_upload_music_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.music.create'))
            ->assertOk()
            ->assertSee('data-cloudinary-direct-upload', false)
            ->assertSee('data-music-cover-preview', false)
            ->assertSee('data-music-audio-preview', false)
            ->assertSee('accept="image/jpeg,image/png,image/webp"', false)
            ->assertSee('accept="audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/mp4,audio/x-m4a"', false)
            ->assertSee('Track Details')
            ->assertSee('Release Assets')
            ->assertSee('Listen Elsewhere')
            ->assertSee('Save Music');
    }

    public function test_regular_user_cannot_open_admin_music_upload_form(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.music.create'))
            ->assertRedirect(route('home'));
    }
}
