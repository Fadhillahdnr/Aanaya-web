<?php

namespace Tests\Feature;

use App\Models\Music;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MusicExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_music_page_renders_cinematic_experience_and_one_global_player(): void
    {
        Cache::flush();

        Music::query()->create([
            'title' => 'Unfold',
            'artist' => 'Aanaya',
            'slug' => 'unfold',
            'cover_image' => '/images/about-image.png',
            'audio_file' => '/uploads/music/unfold.mp3',
            'spotify_link' => 'https://open.spotify.com/track/example',
            'youtube_link' => 'https://youtube.com/watch?v=example',
            'release_date' => '2026-08-01',
        ]);

        $response = $this->get(route('music'));

        $response->assertOk()
            ->assertViewIs('user.music')
            ->assertViewHasAll(['latestMusic', 'recentMusics', 'totalMusic'])
            ->assertDontSee('Singles')
            ->assertDontSee('Albums')
            ->assertSee('data-music-experience', false)
            ->assertSee('data-music-scene="unfoldHero"', false)
            ->assertSee('data-music-scene="unfoldPortal"', false)
            ->assertSee('data-music-scene="msyl"', false)
            ->assertSee('data-music-scene="ending"', false)
            ->assertSee('data-music-player-audio', false)
            ->assertSee('min="0" max="1" value="0.8" step="0.01"', false)
            ->assertSee('Unfold')
            ->assertSee('Spotify ↗')
            ->assertSee('YouTube ↗');

        $this->assertSame(1, substr_count($response->getContent(), '<audio'));
        $this->assertStringNotContainsString('data-aos', $response->getContent());
        $this->assertStringNotContainsString('dream-music-volume-slider', $response->getContent());
    }

    public function test_music_page_has_a_useful_empty_state(): void
    {
        Cache::flush();

        $this->get(route('music'))
            ->assertOk()
            ->assertSee('The next feeling is still being written.')
            ->assertSee('No releases yet.');
    }
}
