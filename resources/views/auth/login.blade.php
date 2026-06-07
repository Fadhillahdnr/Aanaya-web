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
                    <span>Aanaya</span>
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

                        <div class="auth-input-wrapper">

                            <input
                                id="login_password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="auth-input"
                                placeholder="Enter your password">

                            <!-- TOGGLE -->
                            <button type="button"
                                    class="auth-password-toggle"
                                    onclick="togglePassword('login_password', this)">

                                <i class="fas fa-eye"></i>

                            </button>

                        </div>

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

                    <!-- DIVIDER -->
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