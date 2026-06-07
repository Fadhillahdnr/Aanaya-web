<x-guest-layout>

<div class="reset-page">

    <!-- BG -->
    <div class="reset-bg glow-1"></div>
    <div class="reset-bg glow-2"></div>

    <!-- CARD -->
    <div class="reset-card">

        <!-- HEADER -->
        <div class="reset-header">

            <span class="reset-badge">
                ✨ CREATE NEW PASSWORD
            </span>

            <h1>
                Reset Your Password
            </h1>

            <p>
                Create a new secure password and continue
                your dreamy journey in Aanaya Universe.
            </p>

        </div>

        <!-- FORM -->
        <form method="POST"
              action="{{ route('password.store') }}"
              class="reset-form">

            @csrf

            <!-- TOKEN -->
            <input
                type="hidden"
                name="token"
                value="{{ $request->route('token') }}">

            <!-- EMAIL -->
            <div class="reset-group">

                <label>Email Address</label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your email">

                <x-input-error
                    :messages="$errors->get('email')"
                    class="reset-error" />

            </div>

            <!-- PASSWORD -->
            <div class="reset-group">

                <label>New Password</label>

                <div class="reset-input-wrapper">

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create new password">

                    <button
                        type="button"
                        class="reset-password-toggle"
                        onclick="togglePassword('password', this)">

                        <i class="fas fa-eye"></i>

                    </button>

                </div>

                <x-input-error
                    :messages="$errors->get('password')"
                    class="reset-error" />

            </div>

            <!-- CONFIRM -->
            <div class="reset-group">

                <label>Confirm Password</label>

                <div class="reset-input-wrapper">

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm your password">

                    <button
                        type="button"
                        class="reset-password-toggle"
                        onclick="togglePassword('password_confirmation', this)">

                        <i class="fas fa-eye"></i>

                    </button>

                </div>

                <x-input-error
                    :messages="$errors->get('password_confirmation')"
                    class="reset-error" />

            </div>

            <!-- BUTTON -->
            <button
                type="submit"
                class="reset-submit-btn">

                ✨ Reset Password

            </button>

            <!-- BACK -->
            <a href="{{ route('login') }}"
               class="forgot-back-btn">

                <i class="fas fa-arrow-left"></i>

                Back To Login

            </a>

        </form>

    </div>

</div>

<script>
window.togglePassword = function(inputId, button){

    const input = document.getElementById(inputId);

    const icon = button.querySelector('i');

    if(input.type === 'password'){

        input.type = 'text';

        icon.classList.remove('fa-eye');

        icon.classList.add('fa-eye-slash');

    }else{

        input.type = 'password';

        icon.classList.remove('fa-eye-slash');

        icon.classList.add('fa-eye');

    }

}
</script>

</x-guest-layout>