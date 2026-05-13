<x-guest-layout>

    <div class="auth-page">

        <!-- BLUR -->
        <div class="auth-blur auth-blur-1"></div>
        <div class="auth-blur auth-blur-2"></div>

        <!-- CARD -->
        <div class="auth-card">

            <!-- LEFT -->
            <div class="auth-left">

                <span class="auth-tag">
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
                <div class="auth-features">

                    <div class="feature-item">

                        <i class="fas fa-music"></i>

                        <span>
                            Exclusive Music Access
                        </span>

                    </div>

                    <div class="feature-item">

                        <i class="fas fa-image"></i>

                        <span>
                            Dreamy Visual Gallery
                        </span>

                    </div>

                    <div class="feature-item">

                        <i class="fas fa-shirt"></i>

                        <span>
                            Official Merchandise
                        </span>

                    </div>

                </div>

                <!-- MUSIC BARS -->
                <div class="music-bars">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="auth-right">

                <!-- HEADER -->
                <div class="login-header">

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
                      class="auth-form">

                    @csrf

                    <!-- NAME -->
                    <div class="form-group">

                        <label for="name">
                            Full Name
                        </label>

                        <div class="input-wrapper">

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
                                class="auth-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('name')"
                            class="mt-2" />

                    </div>

                    <!-- EMAIL -->
                    <div class="form-group">

                        <label for="email">
                            Email Address
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-envelope"></i>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="Enter your email"
                                class="auth-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2" />

                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-lock"></i>

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Create password"
                                class="auth-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2" />

                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="form-group">

                        <label for="password_confirmation">
                            Confirm Password
                        </label>

                        <div class="input-wrapper">

                            <i class="fas fa-shield-heart"></i>

                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirm password"
                                class="auth-input">

                        </div>

                        <x-input-error
                            :messages="$errors->get('password_confirmation')"
                            class="mt-2" />

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="login-btn-main">

                        <i class="fas fa-user-plus"></i>

                        Create Account

                    </button>

                    <!-- LOGIN -->
                    <div class="register-link">

                        Already have an account?

                        <a href="{{ route('login') }}">
                            Sign In
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-guest-layout>