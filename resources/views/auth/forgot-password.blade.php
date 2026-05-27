<x-guest-layout>

<div class="forgot-page">

    <!-- BACKGROUND -->
    <div class="forgot-bg glow-1"></div>
    <div class="forgot-bg glow-2"></div>

    <!-- CARD -->
    <div class="forgot-card">

        <!-- LOGO / TITLE -->
        <div class="forgot-header">

            <span class="forgot-badge">
                ✨ RESET PASSWORD
            </span>

            <h1>
                Forgot Your Password?
            </h1>

            <p>
                No worries. Enter your email address and
                we’ll send you a dreamy reset link ✨
            </p>

        </div>

        <!-- SESSION STATUS -->
        <x-auth-session-status
            class="forgot-status"
            :status="session('status')" />

        <!-- FORM -->
        <form method="POST"
              action="{{ route('password.email') }}">

            @csrf

            <!-- EMAIL -->
            <div class="forgot-form-group">

                <label for="email">
                    Email Address
                </label>

                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="Enter your email address">

                <x-input-error
                    :messages="$errors->get('email')"
                    class="forgot-error" />

            </div>

            <!-- BUTTON -->
            <button type="submit"
                    class="forgot-submit-btn">

                ✨ Send Reset Link

            </button>

            <!-- BACK BUTTON -->
            <a href="{{ route('login') }}"
            class="forgot-back-btn">

                <i class="fas fa-arrow-left"></i>

                Back to Login

            </a>

        </form>

    </div>

</div>

</x-guest-layout>