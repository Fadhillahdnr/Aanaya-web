<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutExperienceTest extends TestCase
{
    public function test_about_page_renders_the_complete_semantic_experience(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertViewIs('user.about')
            ->assertSee('data-about-experience', false)
            ->assertSee('id="about-title"', false)
            ->assertSee('About')
            ->assertSee('Aanaya')
            ->assertSee('More than')
            ->assertSee('Nostalgia')
            ->assertSee('data-about-character-video', false)
            ->assertSee('assets/character/aanaya.webm', false)
            ->assertSee('Not just')
            ->assertSee('data-footer-experience', false)
            ->assertDontSee('data-aos', false)
            ->assertDontSee('about-story-card', false);
    }
}
