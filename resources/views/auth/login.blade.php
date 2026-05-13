<x-guest-layout>

    <div class="auth-page">

        <!-- BACKGROUND BLUR -->
        <div class="auth-blur auth-blur-1"></div>
        <div class="auth-blur auth-blur-2"></div>

        <!-- LOGIN CARD -->
        <div class="auth-card">

            <!-- LEFT -->
            <div class="auth-left">

                <span class="auth-tag">
                    DREAM POP • CINEMATIC • EMOTIONAL
                </span>

                <h1>
                    Welcome Back to
                    <span>AANAYA</span>
                </h1>

                <p>
                    Continue your dreamy musical journey
                    and explore the emotional universe
                    of Aanaya.
                </p>

                <!-- MUSIC DECOR -->
                <div class="music-bars">

                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="auth-right">

                <div class="login-header">

                    <h2>Login</h2>

                    <p>
                        Access your account
                    </p>

                </div>

                <!-- Session Status -->
                <x-auth-session-status
                    class="mb-4"
                    :status="session('status')" />

                <form method="POST"
                      action="{{ route('login') }}"
                      class="auth-form">

                    @csrf

                    <!-- EMAIL -->
                    <div class="form-group">

                        <label>Email Address</label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="auth-input"
                            placeholder="Enter your email">

                        <x-input-error
                            :messages="$errors->get('email')"
                            class="mt-2" />

                    </div>

                    <!-- PASSWORD -->
                    <div class="form-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="auth-input"
                            placeholder="Enter your password">

                        <x-input-error
                            :messages="$errors->get('password')"
                            class="mt-2" />

                    </div>

                    <!-- OPTIONS -->
                    <div class="auth-options">

                        <label class="remember-box">

                            <input type="checkbox"
                                   name="remember">

                            <span>
                                Remember me
                            </span>

                        </label>

                        @if (Route::has('password.request'))

                            <a href="{{ route('password.request') }}"
                               class="forgot-link">

                                Forgot Password?

                            </a>

                        @endif

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="login-btn-main">

                        Log In

                    </button>

                    <!-- REGISTER -->
                    <div class="register-link">

                        Don’t have an account?

                        <a href="{{ route('register') }}">
                            Create Account
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-guest-layout>