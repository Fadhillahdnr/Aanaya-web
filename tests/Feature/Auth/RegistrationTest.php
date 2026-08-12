<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertStatus(200)
            ->assertSee('data-auth-experience', false)
            ->assertSee('data-password-toggle', false)
            ->assertSee(route('google.login'), false)
            ->assertSee(route('login'), false)
            ->assertDontSee('onclick=', false)
            ->assertDontSee('svgrepo.com', false);
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test.user@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'test.user@gmail.com')->firstOrFail();
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function test_registration_rejects_an_unsupported_email_domain(): void
    {
        $this->post('/register', [
            'name' => 'Temporary Email User',
            'email' => 'temporary@mailinator.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'temporary@mailinator.com']);
    }
}
