<x-guest-layout>

    <div class="auth-regis-page">

        <!-- BLUR -->
        <div class="auth-regis-blur auth-regis-blur-1"></div>
        <div class="auth-regis-blur auth-regis-blur-2"></div>

        <!-- CARD -->
        <div class="auth-regis-card">

            <!-- LEFT -->
            <div class="auth-regis-left">

                <span class="auth-regis-tag">
                    JOIN AANAYA ✨
                </span>

                <h1>
                    Become Part of
                    <span>Aanaya Universe</span>
                </h1>

                <p>
                    Create your account to explore dreamy music,
                    exclusive gallery, latest articles,
                    and official merchandise.
                </p>

                <!-- FEATURES -->
                <div class="auth-regis-features">

                    <!-- ITEM -->
                    <div class="auth-regis-feature-item">

                        <i class="fas fa-music"></i>

                        <div class="auth-regis-feature-content">

                            <h4>
                                Exclusive Music Access
                            </h4>

                            <p>
                                Listen to dreamy tracks, unreleased demos,
                                and emotional soundscapes from Aanaya.
                            </p>

                        </div>

                    </div>

                    <!-- ITEM -->
                    <div class="auth-regis-feature-item">

                        <i class="fas fa-image"></i>

                        <div class="auth-regis-feature-content">

                            <h4>
                                Dreamy Visual Gallery
                            </h4>

                            <p>
                                Explore cinematic visuals, aesthetics,
                                behind the scenes, and emotional moments.
                            </p>

                        </div>

                    </div>

                    <!-- ITEM -->
                    <div class="auth-regis-feature-item">

                        <i class="fas fa-shirt"></i>

                        <div class="auth-regis-feature-content">

                            <h4>
                                Official Merchandise
                            </h4>

                            <p>
                                Discover exclusive apparel and limited
                                collections inspired by Aanaya’s universe.
                            </p>

                        </div>

                    </div>

                </div>

                <!-- MUSIC BARS -->
                <div class="auth-regis-music-bars">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="auth-regis-right">

                <!-- HEADER -->
                <div class="auth-regis-header">

                    <h2>
                        Create Account
                    </h2>

                    <p>
                        Start your dreamy journey today ✨
                    </p>

                </div>

                <!-- FORM -->
                <form method="POST"
                      action="{{ route('register') }}"
                      class="auth-regis-form">

                    @csrf

                    <!-- NAME -->
                    <div class="auth-regis-group">

                        <label for="name">
                            Full Name
                        </label>

                        <div class="auth-regis-input-wrapper">

                            <i class="fas fa-user"></i>

                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Enter your full name"
                                class="auth-regis-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('name')"
                            class="mt-2" />

                    </div>

                    <!-- EMAIL -->
                    <div class="auth-regis-group">

                        <label for="email">
                            Email Address
                        </label>

                        <div class="auth-regis-input-wrapper">

                            <i class="fas fa-envelope"></i>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="Enter your email"
                                class="auth-regis-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2" />

                    </div>

                    <!-- PASSWORD -->
                    <div class="auth-regis-group">

                        <label for="password">
                            Password
                        </label>

                        <div class="auth-regis-input-wrapper">

                            <i class="fas fa-lock"></i>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Create password"
                                class="auth-regis-input">

                            <!-- TOGGLE -->
                            <button type="button"
                                    class="auth-regis-password-toggle"
                                    onclick="togglePassword('password', this)">

                                <i class="fas fa-eye"></i>

                            </button>

                        </div>

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2" />

                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="auth-regis-group">

                        <label for="password_confirmation">
                            Confirm Password
                        </label>

                        <div class="auth-regis-input-wrapper">

                            <i class="fas fa-shield-heart"></i>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm password"
                                class="auth-regis-input">

                            <!-- TOGGLE -->
                            <button type="button"
                                    class="auth-regis-password-toggle"
                                    onclick="togglePassword('password_confirmation', this)">

                                <i class="fas fa-eye"></i>

                            </button>

                        </div>

                        <x-input-error
                            :messages="$errors->get('password_confirmation')"
                            class="mt-2" />

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="auth-regis-btn">

                        <i class="fas fa-user-plus"></i>

                        Create Account

                    </button>
                    
                    <div class="auth-divider">

                        <span>OR</span>

                    </div>

                    <a href="{{ route('google.login') }}"
                        class="auth-google-btn">

                        <img
                            src="https://www.svgrepo.com/show/475656/google-color.svg"
                            alt="Google">

                        Continue with Google

                    </a>

                    <!-- BACK BUTTON -->
                    <a href="{{ route('home') }}"
                    class="forgot-back-btn">

                        <i class="fas fa-arrow-left"></i>

                        Back to Home

                    </a>

                    <!-- LOGIN -->
                    <div class="auth-regis-login-link">

                        Already have an account?

                        <a href="{{ route('login') }}">
                            Sign In
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
    function togglePassword(inputId, button){

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