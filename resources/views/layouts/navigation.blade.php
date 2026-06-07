<nav
    x-data="{
        open: false,
        scrolled: false,
        lastScroll: 0,
        hidden: false
    }"
    x-init="
        window.addEventListener('scroll', () => {

            let currentScroll = window.pageYOffset;

            scrolled = currentScroll > 30;

            if(currentScroll > lastScroll && currentScroll > 120){
                hidden = true;
            }else{
                hidden = false;
            }

            lastScroll = currentScroll;
        });
    "
    :class="{
        'navbar-scrolled': scrolled,
        'navbar-hidden': hidden
    }"
    class="aanaya-navbar">

    <div class="navbar-container">

        <!-- LOGO -->
        <div class="navbar-left">

            <a
                href="{{ route('dashboard') }}"
                class="navbar-logo">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Aanaya Logo">

            </a>

        </div>

        <!-- DESKTOP MENU -->
        <div class="desktop-menu">

            <a
                href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active-link' : '' }}">

                <i class="fas fa-house"></i>

                <span>Dashboard</span>

            </a>

            <a
                href="{{ route('music') }}"
                class="nav-link {{ request()->routeIs('music') ? 'active-link' : '' }}">

                <i class="fas fa-music"></i>

                <span>Music</span>

            </a>

            <a
                href="{{ route('articles') }}"
                class="nav-link {{ request()->routeIs('articles') ? 'active-link' : '' }}">

                <i class="fas fa-newspaper"></i>

                <span>Articles</span>

            </a>

            <a
                href="{{ route('gallery') }}"
                class="nav-link {{ request()->routeIs('gallery') ? 'active-link' : '' }}">

                <i class="fas fa-image"></i>

                <span>Gallery</span>

            </a>

            <a
                href="{{ route('merchandise') }}"
                class="nav-link {{ request()->routeIs('merchandise') ? 'active-link' : '' }}">

                <i class="fas fa-bag-shopping"></i>

                <span>Merch</span>

            </a>

            <a
                href="{{ route('about') }}"
                class="nav-link {{ request()->routeIs('about') ? 'active-link' : '' }}">

                <i class="fas fa-heart"></i>

                <span>About</span>

            </a>

        </div>

        <!-- RIGHT -->
        <div class="navbar-right">

            @auth

                <!-- USER -->
                <div
                    class="user-dropdown"
                    x-data="{ dropdown: false }">

                    <button
                        @click="dropdown = !dropdown"
                        class="user-btn">

                        @auth

                        <a
                            href="{{ route('profile.edit') }}"
                            class="navbar-avatar-link">

                            @if(Auth::user()->avatar_url)

                                <img
                                    src="{{ Auth::user()->avatar_url }}"
                                    class="navbar-user-avatar"
                                    alt="{{ Auth::user()->name }}">

                            @else

                                <div
                                    class="navbar-user-avatar-fallback">

                                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}

                                </div>

                            @endif

                        </a>

                        @endauth

                        <i class="fas fa-chevron-down"></i>

                    </button>

                    <!-- DROPDOWN -->
                    <div
                        x-show="dropdown"
                        x-transition:enter="dropdown-enter"
                        x-transition:enter-start="dropdown-enter-start"
                        x-transition:enter-end="dropdown-enter-end"
                        x-transition:leave="dropdown-leave"
                        x-transition:leave-start="dropdown-leave-start"
                        x-transition:leave-end="dropdown-leave-end"
                        @click.outside="dropdown = false"
                        class="dropdown-menu">

                        <a href="{{ route('profile.edit') }}">

                            <i class="fas fa-user"></i>

                            Profile

                        </a>

                        @if(Auth::user()->role === 'admin')

                            <a href="/admin">

                                <i class="fas fa-shield-heart"></i>

                                Admin Panel

                            </a>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button type="submit">

                                <i class="fas fa-right-from-bracket"></i>

                                Logout

                            </button>

                        </form>

                    </div>

                </div>

            @else

                <!-- GUEST -->
                <div class="guest-buttons">

                    <a
                        href="{{ route('login') }}"
                        class="login-btn">

                        Login

                    </a>

                    <a
                        href="{{ route('register') }}"
                        class="register-btn">

                        Register

                    </a>

                </div>


            @endauth

            <!-- MOBILE TOGGLE -->
            <button
                @click="open = !open"
                class="mobile-toggle">

                <i class="fas fa-bars"></i>

            </button>

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div
        x-show="open"
        x-transition
        class="mobile-menu">

        <a href="{{ route('dashboard') }}">

            <i class="fas fa-house"></i>

            Dashboard

        </a>

        <a href="{{ route('music') }}">

            <i class="fas fa-music"></i>

            Music

        </a>

        <a href="{{ route('articles') }}">

            <i class="fas fa-newspaper"></i>

            Articles

        </a>

        <a href="{{ route('gallery') }}">

            <i class="fas fa-image"></i>

            Gallery

        </a>

        <a href="{{ route('merchandise') }}">

            <i class="fas fa-bag-shopping"></i>

            Merchandise

        </a>

        <a href="{{ route('about') }}">

            <i class="fas fa-heart"></i>

            About

        </a>

        @auth

            <a href="{{ route('profile.edit') }}">

                <i class="fas fa-user"></i>

                Profile

            </a>

            @if(Auth::user()->role === 'admin')

                <a href="/admin">

                    <i class="fas fa-shield-heart"></i>

                    Admin Panel

                </a>

            @endif

            <form
                method="POST"
                action="{{ route('logout') }}">

                @csrf

                <button type="submit">

                    <i class="fas fa-right-from-bracket"></i>

                    Logout

                </button>

            </form>

        @else

            <a href="{{ route('login') }}">

                Login

            </a>

            <a href="{{ route('register') }}">

                Register

            </a>

        @endauth

    </div>

</nav>