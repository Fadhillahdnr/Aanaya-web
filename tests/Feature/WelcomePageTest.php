<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_root_renders_the_aanaya_welcome_experience(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertViewIs('welcome')
            ->assertSee('data-welcome-experience', false)
            ->assertSee('AANAYA')
            ->assertSee(route('dashboard'), false)
            ->assertSee(route('music'), false)
            ->assertSee(route('articles'), false)
            ->assertSee(route('gallery'), false)
            ->assertSee(route('merchandise'), false)
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false);
    }
}
