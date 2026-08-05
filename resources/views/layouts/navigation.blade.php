<nav
    x-data="{
        open: false,
        scrolled: false,
        lastScroll: 0,
        hidden: false,
        openMenu() {
            this.open = true;
            this.hidden = false;
            this.$nextTick(() => this.$refs.mobileClose?.focus());
        },
        closeMenu(returnFocus = false) {
            this.open = false;
            if (returnFocus) {
                this.$nextTick(() => this.$refs.mobileToggle?.focus());
            }
        }
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
    x-effect="document.body.classList.toggle('mobile-navigation-open', open)"
    @keydown.escape.window="open && closeMenu(true)"
    @resize.window="if (window.innerWidth > 1100 && open) closeMenu()"
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
                class="nav-link {{ request()->routeIs('merchandise*') || request()->routeIs('orders.*') ? 'active-link' : '' }}">

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
                @php
                    $navbarUser = Auth::user();
                    $navbarNameParts = preg_split('/\s+/', trim($navbarUser->name)) ?: [];
                    $navbarInitials = collect($navbarNameParts)
                        ->filter()
                        ->take(2)
                        ->map(fn ($namePart) => mb_strtoupper(mb_substr($namePart, 0, 1)))
                        ->implode('');
                    $navbarAvatarUrl = $navbarUser->avatar_url;
                    $hasNavbarAvatar = $navbarAvatarUrl
                        && $navbarAvatarUrl !== asset('assets/default-avatar.png');
                @endphp

                <!-- USER -->
                <div
                    class="user-dropdown"
                    x-data="{ dropdown: false }">

                    <button
                        type="button"
                        @click="dropdown = !dropdown"
                        @keydown.escape.stop="dropdown = false"
                        :aria-expanded="dropdown.toString()"
                        aria-controls="navbar-user-menu"
                        aria-haspopup="menu"
                        class="user-btn">

                        <span class="navbar-user-avatar-shell" aria-hidden="true">
                            <span class="navbar-user-avatar-fallback">
                                {{ $navbarInitials ?: 'A' }}
                            </span>

                            @if($hasNavbarAvatar)
                                <img
                                    src="{{ $navbarAvatarUrl }}"
                                    class="navbar-user-avatar"
                                    alt=""
                                    x-on:error="$el.remove()">
                            @endif
                        </span>

                        <span class="navbar-user-copy">
                            <small>Welcome back</small>
                            <strong>{{ $navbarUser->name }}</strong>
                        </span>

                        <i class="fas fa-chevron-down navbar-user-chevron" :class="{ 'is-open': dropdown }"></i>

                    </button>

                    <!-- DROPDOWN -->
                    <div
                        id="navbar-user-menu"
                        x-cloak
                        x-show="dropdown"
                        x-transition:enter="dropdown-enter"
                        x-transition:enter-start="dropdown-enter-start"
                        x-transition:enter-end="dropdown-enter-end"
                        x-transition:leave="dropdown-leave"
                        x-transition:leave-start="dropdown-leave-start"
                        x-transition:leave-end="dropdown-leave-end"
                        @click.outside="dropdown = false"
                        class="dropdown-menu"
                        role="menu">

                        <div class="dropdown-user-summary">
                            <span class="dropdown-user-avatar" aria-hidden="true">
                                <span>{{ $navbarInitials ?: 'A' }}</span>
                                @if($hasNavbarAvatar)
                                    <img src="{{ $navbarAvatarUrl }}" alt="" x-on:error="$el.remove()">
                                @endif
                            </span>
                            <span>
                                <strong>{{ $navbarUser->name }}</strong>
                                <small>{{ $navbarUser->email }}</small>
                            </span>
                        </div>

                        <a href="{{ route('profile.edit') }}" role="menuitem">

                            <i class="fas fa-user"></i>

                            Profile

                        </a>

                        <a href="{{ route('orders.index') }}" role="menuitem">

                            <i class="fas fa-receipt"></i>

                            My Orders

                        </a>

                        @if(Auth::user()->role === 'admin')

                            <a href="/admin" role="menuitem">

                                <i class="fas fa-shield-heart"></i>

                                Admin Panel

                            </a>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button type="submit" role="menuitem">

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
                type="button"
                x-ref="mobileToggle"
                @click="open ? closeMenu() : openMenu()"
                :aria-expanded="open.toString()"
                aria-controls="user-mobile-sidebar"
                aria-label="Open navigation menu"
                class="mobile-toggle">

                <i class="fas fa-bars"></i>

            </button>

        </div>

    </div>

    <!-- MOBILE SIDEBAR -->
    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            class="mobile-drawer-layer"
            aria-labelledby="user-mobile-sidebar-title">

            <button
                type="button"
                x-show="open"
                x-transition.opacity.duration.200ms
                @click="closeMenu(true)"
                class="mobile-drawer-scrim"
                aria-label="Close navigation menu">
            </button>

            <aside
                id="user-mobile-sidebar"
                x-show="open"
                x-transition:enter="mobile-drawer-enter"
                x-transition:enter-start="mobile-drawer-enter-start"
                x-transition:enter-end="mobile-drawer-enter-end"
                x-transition:leave="mobile-drawer-leave"
                x-transition:leave-start="mobile-drawer-leave-start"
                x-transition:leave-end="mobile-drawer-leave-end"
                @click.outside="closeMenu()"
                class="mobile-drawer"
                role="dialog"
                aria-modal="true">

                <div class="mobile-drawer-header">
                    <a href="{{ route('dashboard') }}" class="mobile-drawer-brand" @click="closeMenu()">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                        <span>
                            <small>Welcome to</small>
                            <strong id="user-mobile-sidebar-title">Aanaya Universe</strong>
                        </span>
                    </a>

                    <button
                        type="button"
                        x-ref="mobileClose"
                        @click="closeMenu(true)"
                        class="mobile-drawer-close"
                        aria-label="Close navigation menu">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                @auth
                    <a href="{{ route('profile.edit') }}" class="mobile-drawer-profile" @click="closeMenu()">
                        <span class="mobile-drawer-avatar-shell" aria-hidden="true">
                            <span class="mobile-drawer-avatar-fallback">{{ $navbarInitials ?: 'A' }}</span>
                            @if($hasNavbarAvatar)
                                <img src="{{ $navbarAvatarUrl }}" alt="" x-on:error="$el.remove()">
                            @endif
                        </span>
                        <span>
                            <strong>{{ $navbarUser->name }}</strong>
                            <small>View your profile</small>
                        </span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @endauth

                <nav class="mobile-drawer-nav" aria-label="Mobile navigation">
                    <span class="mobile-drawer-label">Explore</span>

                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-house"></i><span>Dashboard</span>
                    </a>
                    <a href="{{ route('music') }}" class="{{ request()->routeIs('music') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-music"></i><span>Music</span>
                    </a>
                    <a href="{{ route('articles') }}" class="{{ request()->routeIs('articles') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-newspaper"></i><span>Articles</span>
                    </a>
                    <a href="{{ route('gallery') }}" class="{{ request()->routeIs('gallery') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-image"></i><span>Gallery</span>
                    </a>
                    <a href="{{ route('merchandise') }}" class="{{ request()->routeIs('merchandise*') || request()->routeIs('orders.*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-bag-shopping"></i><span>Merchandise</span>
                    </a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-heart"></i><span>About</span>
                    </a>

                    @auth
                        <span class="mobile-drawer-label mobile-drawer-label-account">Account</span>
                        <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}" @click="closeMenu()">
                            <i class="fas fa-receipt"></i><span>My Orders</span>
                        </a>
                        @if(Auth::user()->role === 'admin')
                            <a href="/admin" @click="closeMenu()">
                                <i class="fas fa-shield-heart"></i><span>Admin Panel</span>
                            </a>
                        @endif
                    @endauth
                </nav>

                <div class="mobile-drawer-footer">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="mobile-drawer-logout">
                                <i class="fas fa-right-from-bracket"></i><span>Logout</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="mobile-drawer-login" @click="closeMenu()">Login</a>
                        <a href="{{ route('register') }}" class="mobile-drawer-register" @click="closeMenu()">Create account</a>
                    @endauth
                </div>
            </aside>
        </div>
    </template>

</nav>
