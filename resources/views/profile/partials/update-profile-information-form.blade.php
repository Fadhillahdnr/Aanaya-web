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

            <label for="name">Name</label>

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

            <label for="email">Email</label>

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
                        <strong>Email not verified.</strong>
                        Send a secure link to confirm that this email belongs to you.
                    </p>

                    <button form="send-verification"
                            class="user-profile-verify-btn">

                        <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        Send Verification Email

                    </button>

                </div>

                @if (session('status') === 'verification-link-sent')

                    <div class="user-profile-success">

                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                        Verification link sent. Please check your inbox and spam folder.

                    </div>

                @endif

            @elseif ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                <div class="user-profile-verify user-profile-verify--complete">
                    <p>
                        <i class="fas fa-circle-check" aria-hidden="true"></i>
                        <strong>Email verified.</strong>
                        Your email ownership has been confirmed.
                    </p>
                </div>
            @endif

        </div>

        <div class="user-profile-section-heading">
            <div class="user-profile-section-icon" aria-hidden="true">
                <i class="fas fa-heart"></i>
            </div>
            <div>
                <h3>Personal Details</h3>
                <p>These details help make checkout quicker and more personal.</p>
            </div>
        </div>

        <div class="user-profile-fields-grid">
            <div class="user-profile-group">
                <label for="phone">Phone Number</label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    class="user-profile-input"
                    value="{{ old('phone', $user->phone) }}"
                    inputmode="tel"
                    autocomplete="tel"
                    placeholder="e.g. 08123456789"
                    aria-describedby="phone-help">
                <p id="phone-help" class="user-profile-field-help">Used for shipping updates and checkout.</p>
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div class="user-profile-group">
                <label for="date_of_birth">Date of Birth</label>
                <input
                    id="date_of_birth"
                    name="date_of_birth"
                    type="date"
                    class="user-profile-input"
                    value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                    max="{{ now()->toDateString() }}"
                    autocomplete="bday">
                <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
            </div>

            <div class="user-profile-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" class="user-profile-input">
                    <option value="">Choose an option</option>
                    <option value="female" @selected(old('gender', $user->gender) === 'female')>Female</option>
                    <option value="male" @selected(old('gender', $user->gender) === 'male')>Male</option>
                    <option value="non_binary" @selected(old('gender', $user->gender) === 'non_binary')>Non-binary</option>
                    <option value="prefer_not_to_say" @selected(old('gender', $user->gender) === 'prefer_not_to_say')>Prefer not to say</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>

            <div class="user-profile-group user-profile-group--wide">
                <label for="address">Address</label>
                <textarea
                    id="address"
                    name="address"
                    class="user-profile-input user-profile-textarea"
                    rows="4"
                    maxlength="1000"
                    autocomplete="street-address"
                    placeholder="Street, building number, district, city, and postal code">{{ old('address', $user->address) }}</textarea>
                <p class="user-profile-field-help">You can still use a different delivery address during checkout.</p>
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
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
