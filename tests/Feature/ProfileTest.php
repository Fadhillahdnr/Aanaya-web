<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
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

    public function test_profile_page_exposes_camera_and_gallery_photo_actions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('data-open-profile-camera', false)
            ->assertSee('data-choose-profile-photo', false)
            ->assertSee('data-profile-camera-dialog', false)
            ->assertSee('capture="user"', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test.profile@gmail.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test.profile@gmail.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_personal_profile_details_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '+62 812-3456-7890',
                'address' => 'Jl. Melati No. 12, Bandung, Jawa Barat 40123',
                'gender' => 'female',
                'date_of_birth' => '2000-05-18',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('+62 812-3456-7890', $user->phone);
        $this->assertSame('Jl. Melati No. 12, Bandung, Jawa Barat 40123', $user->address);
        $this->assertSame('female', $user->gender);
        $this->assertSame('2000-05-18', $user->date_of_birth->toDateString());
    }

    public function test_profile_rejects_an_invalid_phone_gender_and_future_birth_date(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => 'not-a-phone',
                'gender' => 'unknown',
                'date_of_birth' => now()->addDay()->toDateString(),
            ])
            ->assertRedirect('/profile')
            ->assertSessionHasErrors(['phone', 'gender', 'date_of_birth']);
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

    public function test_navigation_uses_initials_when_user_has_no_profile_photo(): void
    {
        $user = User::factory()->create([
            'name' => 'Muhamad Fadhillah Dinurahman',
            'avatar' => null,
            'profile_photo' => null,
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('MF')
            ->assertSee('Muhamad Fadhillah Dinurahman')
            ->assertDontSee('assets/default-avatar.png', false);
    }

    public function test_navigation_prioritizes_uploaded_profile_photo_over_google_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => 'https://example.com/google-avatar.jpg',
            'profile_photo' => 'https://res.cloudinary.com/demo/image/upload/uploaded-profile.jpg',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('https://res.cloudinary.com/demo/image/upload/uploaded-profile.jpg', false)
            ->assertDontSee('https://example.com/google-avatar.jpg', false);
    }

    public function test_uploaded_profile_photo_replaces_google_avatar(): void
    {
        $user = User::factory()->create([
            'avatar' => 'https://example.com/google-avatar.jpg',
        ]);

        $media = Media::create([
            'uploaded_by' => $user->id,
            'public_id' => 'development/users/avatars/avatar',
            'resource_type' => 'image',
            'media_type' => 'image',
            'purpose' => 'profile_photo',
            'original_name' => 'avatar.jpg',
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1000,
            'secure_url' => 'https://res.cloudinary.com/demo/image/upload/avatar.jpg',
            'status' => 'ready',
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'uploaded_media' => ['profile_photo' => $media->id],
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
            'development/users/avatars/avatar',
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
