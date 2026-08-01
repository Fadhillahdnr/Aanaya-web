<section>

    <form id="send-verification"
          method="post"
          action="{{ route('verification.send') }}">

        @csrf

    </form>

    <form method="post" data-cloudinary-direct-upload
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="user-profile-form">

        @csrf
        @method('patch')

        <!-- PHOTO -->

        <div class="user-profile-group">

            <label id="profile-photo-label">Profile Photo</label>

            <div class="profile-photo-upload" data-profile-photo-preview>

                @if($user->profile_photo || $user->avatar)

                    <img
                        src="{{ $user->avatar_url }}"
                        class="profile-photo-preview"
                        alt="Current profile photo for {{ $user->name }}">

                @else

                    <div class="profile-photo-placeholder">

                        {{ strtoupper(substr($user->name,0,1)) }}

                    </div>

                @endif

            </div>

            <div class="profile-photo-actions" aria-labelledby="profile-photo-label">
                <button
                    type="button"
                    class="profile-photo-action profile-photo-action--primary"
                    data-open-profile-camera>
                    <i class="fas fa-camera" aria-hidden="true"></i>
                    <span>Open Camera</span>
                </button>

                <button
                    type="button"
                    class="profile-photo-action"
                    data-choose-profile-photo>
                    <i class="fas fa-images" aria-hidden="true"></i>
                    <span>Choose from Gallery</span>
                </button>
            </div>

            <input
                id="profile_photo"
                type="file"
                name="profile_photo"
                accept="image/*"
                class="profile-photo-file-input"
                aria-describedby="profile-photo-help">

            <input
                type="file"
                accept="image/*"
                capture="user"
                class="profile-photo-file-input"
                data-camera-capture-fallback
                tabindex="-1"
                aria-hidden="true">

            <p id="profile-photo-help" class="profile-photo-help">
                Take a new photo or choose JPG, PNG, or WebP. The image will be previewed before saving.
            </p>

            <p class="profile-photo-feedback" data-profile-photo-feedback aria-live="polite"></p>

            <x-input-error
                class="mt-2"
                :messages="$errors->get('profile_photo')" />

        </div>

        <!-- NAME -->
        <div class="user-profile-group">

            <label>Name</label>

            <input
                id="name"
                name="name"
                type="text"
                class="user-profile-input"
                value="{{ old('name', $user->name) }}"
                required
                autofocus>

            <x-input-error
                class="mt-2"
                :messages="$errors->get('name')" />

        </div>

        <!-- EMAIL -->
        <div class="user-profile-group">

            <label>Email</label>

            <input
                id="email"
                name="email"
                type="email"
                class="user-profile-input"
                value="{{ old('email', $user->email) }}"
                required>

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())

                <div class="user-profile-verify">

                    <p>
                        Your email address is unverified.
                    </p>

                    <button form="send-verification"
                            class="user-profile-verify-btn">

                        Re-send Verification Email

                    </button>

                </div>

                @if (session('status') === 'verification-link-sent')

                    <div class="user-profile-success">

                        Verification link sent successfully ✨

                    </div>

                @endif

            @endif

        </div>

        <!-- BUTTON -->
        <div class="user-profile-actions">

            <button class="user-profile-btn">

                Save Changes

            </button>

            @if (session('status') === 'profile-updated')

                <span class="user-profile-saved">

                    Saved ✨

                </span>

            @endif

        </div>

    </form>

    <dialog class="profile-camera-dialog" data-profile-camera-dialog aria-labelledby="profile-camera-title">
        <div class="profile-camera-shell">
            <div class="profile-camera-header">
                <div>
                    <h3 id="profile-camera-title">Take Profile Photo</h3>
                    <p>Position your face inside the frame, then capture the photo.</p>
                </div>

                <button type="button" class="profile-camera-close" data-close-profile-camera aria-label="Close camera">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <div class="profile-camera-viewport">
                <video data-profile-camera-video autoplay muted playsinline></video>
                <div class="profile-camera-guide" aria-hidden="true"></div>
                <div class="profile-camera-state" data-profile-camera-state role="status">
                    Preparing camera…
                </div>
            </div>

            <canvas data-profile-camera-canvas hidden></canvas>

            <div class="profile-camera-actions">
                <button type="button" class="profile-camera-secondary" data-close-profile-camera>
                    Cancel
                </button>
                <button type="button" class="profile-camera-capture" data-capture-profile-photo disabled>
                    <i class="fas fa-camera" aria-hidden="true"></i>
                    Capture Photo
                </button>
            </div>
        </div>
    </dialog>

</section>
