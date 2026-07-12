<?php

namespace Tests\Feature;

use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_google_avatar_is_displayed_before_a_custom_photo_is_uploaded(): void
    {
        $user = User::factory()->create([
            'avatar' => 'https://example.com/google-avatar.jpg',
            'profile_photo' => null,
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('https://example.com/google-avatar.jpg', false);
    }

    public function test_uploaded_profile_photo_replaces_google_avatar(): void
    {
        $uploadApi = Mockery::mock();
        $uploadApi->shouldReceive('upload')
            ->once()
            ->with(Mockery::type('string'), [
                'folder' => 'aanaya/profile-photos',
            ])
            ->andReturn([
                'secure_url' => 'https://res.cloudinary.com/demo/image/upload/avatar.jpg',
                'public_id' => 'aanaya/profile-photos/avatar',
            ]);

        $cloudinary = Mockery::mock();
        $cloudinary->shouldReceive('uploadApi')
            ->once()
            ->andReturn($uploadApi);

        $this->app->instance(Cloudinary::class, $cloudinary);

        $user = User::factory()->create([
            'avatar' => 'https://example.com/google-avatar.jpg',
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame(
            'https://res.cloudinary.com/demo/image/upload/avatar.jpg',
            $user->avatar_url
        );
        $this->assertSame(
            'aanaya/profile-photos/avatar',
            $user->profile_photo_public_id
        );
    }

    public function test_google_avatar_is_used_when_uploaded_photo_file_is_missing(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar' => 'https://example.com/google-avatar.jpg',
            'profile_photo' => 'profile-photos/missing.jpg',
        ]);

        $this->assertSame(
            'https://example.com/google-avatar.jpg',
            $user->avatar_url
        );
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
