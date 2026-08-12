<?php

namespace Tests\Feature;

use Tests\TestCase;

class FooterExperienceTest extends TestCase
{
    public function test_user_footer_contains_real_navigation_and_safe_social_links(): void
    {
        $footer = $this->view('layouts.footer');

        $footer
            ->assertSee('data-footer-experience', false)
            ->assertSee(route('dashboard'), false)
            ->assertSee(route('music'), false)
            ->assertSee(route('articles'), false)
            ->assertSee(route('gallery'), false)
            ->assertSee(route('merchandise'), false)
            ->assertSee(route('about'), false)
            ->assertSee('href="#page-top"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('YouTube')
            ->assertSee('Spotify')
            ->assertSee('WhatsApp')
            ->assertSee('TikTok')
            ->assertSee('Instagram')
            ->assertDontSee('data-aos', false)
            ->assertDontSee('transparenttextures.com', false);
    }
}
