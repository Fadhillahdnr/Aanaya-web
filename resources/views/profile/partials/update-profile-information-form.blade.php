<section>

    <form id="send-verification"
          method="post"
          action="{{ route('verification.send') }}">

        @csrf

    </form>

    <form method="post"
        action="{{ route('profile.update') }}"
        enctype="multipart/form-data"
        class="user-profile-form">

        @csrf
        @method('patch')

        <!-- PHOTO -->

        <div class="user-profile-group">

            <label>Profile Photo</label>

            <div class="profile-photo-upload">

                @if($user->profile_photo)

                    <img
                        src="{{ asset('storage/'.$user->profile_photo) }}"
                        class="profile-photo-preview">

                @else

                    <div class="profile-photo-placeholder">

                        {{ strtoupper(substr($user->name,0,1)) }}

                    </div>

                @endif

            </div>

            <input
                type="file"
                name="profile_photo"
                accept="image/*"
                class="user-profile-input">

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

</section>