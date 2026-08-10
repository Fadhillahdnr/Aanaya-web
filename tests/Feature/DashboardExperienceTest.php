<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_the_editorial_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('data-dashboard-experience', false)
            ->assertSee('data-dashboard-chapter-indicator', false)
            ->assertSee('data-dashboard-chapter="Music"', false)
            ->assertSee('data-explore-paper-trail', false)
            ->assertDontSee('data-explore-preview', false)
            ->assertDontSee('data-explore-aura', false)
            ->assertDontSee('data-explore-creature', false)
            ->assertSee('A dream')
            ->assertSee('Choose your way')
            ->assertSee('Meet the souls')
            ->assertSee('Songs made')
            ->assertSee('KEEP');
    }

    public function test_authenticated_dashboard_keeps_the_personal_greeting(): void
    {
        $user = User::factory()->create(['name' => 'Dream Listener']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Hello, Dream Listener');
    }
}
