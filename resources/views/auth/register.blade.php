<x-guest-layout>
    <div class="auth-experience auth-experience--register" data-auth-experience>
        <header class="auth-experience__nav">
            <a href="{{ route('home') }}" class="auth-brand" aria-label="Aanaya home">
                <img src="{{ asset('images/logo.png') }}" alt="" width="48" height="48">
                <span>Aanaya</span>
            </a>
            <a href="{{ route('login') }}" class="auth-nav-link">Sign in <span aria-hidden="true">↗</span></a>
        </header>

        <main class="auth-experience__layout" id="auth-main">
            <section class="auth-story" aria-labelledby="register-story-title">
                <div class="auth-story__atmosphere" aria-hidden="true">
                    <span class="auth-orbit auth-orbit--one" data-auth-depth="0.35"></span>
                    <span class="auth-orbit auth-orbit--two" data-auth-depth="0.7"></span>
                    <span class="auth-note" data-auth-depth="1">A / 02</span>
                </div>
                <div class="auth-story__copy" data-auth-reveal>
                    <p class="auth-kicker">A new chapter begins</p>
                    <h1 id="register-story-title">Enter the<br><em>universe.</em></h1>
                    <p>Create an account and make the music, stories, and visual world of Aanaya yours.</p>
                </div>
                <div class="auth-story__chapters" aria-hidden="true">
                    <span>Music</span><span>Stories</span><span>Feelings</span>
                </div>
            </section>

            <section class="auth-panel" aria-labelledby="register-heading">
                <div class="auth-panel__inner" data-auth-form>
                    <p class="auth-panel__eyebrow">Begin here</p>
                    <h2 id="register-heading">Create your account</h2>
                    <p class="auth-panel__intro">A few details, then the universe is yours.</p>

                    <form method="POST" action="{{ route('register') }}" class="auth-form" data-auth-form-element>
                        @csrf

                        <div class="auth-field">
                            <label for="register_name">Full name</label>
                            <input id="register_name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                autocomplete="name" aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                                @if ($errors->has('name')) aria-describedby="register_name_error" @endif placeholder="Your full name">
                            <div id="register_name_error" class="auth-field__error" role="alert"><x-input-error :messages="$errors->get('name')" /></div>
                        </div>

                        <div class="auth-field">
                            <label for="register_email">Email address</label>
                            <input id="register_email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="username" inputmode="email" aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                                aria-describedby="register_email_help{{ $errors->has('email') ? ' register_email_error' : '' }}" placeholder="you@example.com">
                            <p id="register_email_help" class="auth-field__help">Use an active email from a trusted provider. You can verify it from your profile.</p>
                            <div id="register_email_error" class="auth-field__error" role="alert"><x-input-error :messages="$errors->get('email')" /></div>
                        </div>

                        <div class="auth-form__password-grid">
                            <div class="auth-field">
                                <label for="register_password">Password</label>
                                <div class="auth-field__control">
                                    <input id="register_password" type="password" name="password" required autocomplete="new-password"
                                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                                        @if ($errors->has('password')) aria-describedby="register_password_error" @endif placeholder="Create password">
                                    <button type="button" class="auth-password-toggle" data-password-toggle data-password-target="register_password" aria-label="Show password" aria-pressed="false">
                                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.75"/></svg>
                                    </button>
                                </div>
                                <div id="register_password_error" class="auth-field__error" role="alert"><x-input-error :messages="$errors->get('password')" /></div>
                            </div>

                            <div class="auth-field">
                                <label for="password_confirmation">Confirm password</label>
                                <div class="auth-field__control">
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                                    <button type="button" class="auth-password-toggle" data-password-toggle data-password-target="password_confirmation" aria-label="Show password" aria-pressed="false">
                                        <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.75"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="auth-submit" data-auth-submit>
                            <span data-submit-label>Join Aanaya</span><span aria-hidden="true" data-submit-arrow>→</span>
                            <span class="auth-submit__loader" aria-hidden="true"></span>
                        </button>

                        <div class="auth-divider"><span>or continue with</span></div>
                        <a href="{{ route('google.login') }}" class="auth-google">
                            <svg aria-hidden="true" viewBox="0 0 24 24"><path fill="#4285F4" d="M21.6 12.2c0-.7-.1-1.5-.2-2.2H12v4.3h5.4a4.6 4.6 0 0 1-2 3v2.8h3.6c2.1-2 3.3-4.8 3.3-7.9Z"/><path fill="#34A853" d="M12 22c3 0 5.5-1 7.3-2.7l-3.6-2.8c-1 .7-2.3 1-3.7 1-2.9 0-5.3-1.9-6.2-4.5H2.1v2.9A11 11 0 0 0 12 22Z"/><path fill="#FBBC05" d="M5.8 13a6.6 6.6 0 0 1 0-4.2V6H2.1a11 11 0 0 0 0 9.8L5.8 13Z"/><path fill="#EA4335" d="M12 4.4c1.6 0 3 .5 4.2 1.6l3.1-3.1A10.5 10.5 0 0 0 2.1 6l3.7 2.8C6.7 6.2 9.1 4.4 12 4.4Z"/></svg>
                            Continue with Google
                        </a>
                        <p class="auth-switch">Already part of Aanaya? <a href="{{ route('login') }}">Sign in</a></p>
                    </form>
                </div>
            </section>
        </main>
    </div>
</x-guest-layout>
