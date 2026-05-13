<section>

    <form method="post"
          action="{{ route('password.update') }}"
          class="user-profile-form">

        @csrf
        @method('put')

        <!-- CURRENT -->
        <div class="user-profile-group">

            <label>Current Password</label>

            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="user-profile-input">

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2" />

        </div>

        <!-- NEW -->
        <div class="user-profile-group">

            <label>New Password</label>

            <input
                id="update_password_password"
                name="password"
                type="password"
                class="user-profile-input">

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2" />

        </div>

        <!-- CONFIRM -->
        <div class="user-profile-group">

            <label>Confirm Password</label>

            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="user-profile-input">

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2" />

        </div>

        <!-- BUTTON -->
        <div class="user-profile-actions">

            <button class="user-profile-btn">

                Update Password

            </button>

            @if (session('status') === 'password-updated')

                <span class="user-profile-saved">

                    Password Updated ✨

                </span>

            @endif

        </div>

    </form>

</section>