<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\MediaService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, MediaService $mediaService): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->except('profile_photo'));

        $oldPhoto = $user->profile_photo;
        $oldPublicId = $user->profile_photo_public_id;

        $media = $mediaService->fromRequest($request, 'profile_photo');

        if ($media) {
            $user->profile_photo = $media->secure_url;
            $user->profile_photo_public_id = $media->public_id;
        }

        if (
            $user->isDirty('email')
        ) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($media) {
            $mediaService->claim($media, $user, 'profile_photo');
            if ($oldPublicId) {
                $mediaService->queueDelete($oldPublicId);
            } elseif ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                // Clean up photos created by the previous local-storage flow.
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        return Redirect::route('profile.edit')
            ->with(
                'status',
                'profile-updated'
            );
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request, MediaService $mediaService): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->profile_photo_public_id) {
            $mediaService->queueDelete($user->profile_photo_public_id);
        } elseif (
            $user->profile_photo &&
            Storage::disk('public')->exists($user->profile_photo)
        ) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
