@php
    $navigationItems = [
        ['number' => '01', 'label' => 'Home', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard'), 'icon' => 'fa-house'],
        ['number' => '02', 'label' => 'Music', 'route' => 'music', 'active' => request()->routeIs('music'), 'icon' => 'fa-music'],
        ['number' => '03', 'label' => 'Stories', 'route' => 'articles', 'active' => request()->routeIs('articles*'), 'icon' => 'fa-book-open'],
        ['number' => '04', 'label' => 'Gallery', 'route' => 'gallery', 'active' => request()->routeIs('gallery'), 'icon' => 'fa-images'],
        ['number' => '05', 'label' => 'Merch', 'route' => 'merchandise', 'active' => request()->routeIs('merchandise*') || request()->routeIs('cart.*') || request()->routeIs('checkout*') || request()->routeIs('orders.*'), 'icon' => 'fa-bag-shopping'],
        ['number' => '06', 'label' => 'About', 'route' => 'about', 'active' => request()->routeIs('about'), 'icon' => 'fa-heart'],
    ];
    $navbarCartCount = collect(session('cart', []))->sum(fn ($item) => (int) ($item['quantity'] ?? 1));

    if (Auth::check()) {
        $navbarUser = Auth::user();
        $navbarNameParts = preg_split('/\s+/', trim($navbarUser->name)) ?: [];
        $navbarInitials = collect($navbarNameParts)
            ->filter()
            ->take(2)
            ->map(fn ($namePart) => mb_strtoupper(mb_substr($namePart, 0, 1)))
            ->implode('');
        $navbarAvatarUrl = $navbarUser->avatar_url;
        $hasNavbarAvatar = $navbarAvatarUrl && $navbarAvatarUrl !== asset('assets/default-avatar.png');
    }
@endphp

<nav
    x-data="{
        open: false,
        openMenu() {
            this.open = true;
            this.$nextTick(() => this.$refs.drawerClose?.focus());
        },
        closeMenu(returnFocus = false) {
            this.open = false;
            if (returnFocus) this.$nextTick(() => (this.$refs.mobileToggle || this.$refs.railToggle)?.focus());
        },
        trapFocus(event) {
            const focusable = [...this.$refs.drawer.querySelectorAll('a[href], button:not([disabled])')]
                .filter((element) => element.offsetParent !== null);
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    }"
    x-effect="document.body.classList.toggle('aanaya-navigation-open', open)"
    @keydown.escape.window="open && closeMenu(true)"
    class="aanaya-navigation"
    aria-label="Primary navigation">

    <span class="aanaya-rail-sensor" aria-hidden="true"></span>

    <aside class="aanaya-nav-rail" aria-label="Quick navigation">
        <a href="{{ route('dashboard') }}" class="aanaya-nav-logo" aria-label="Aanaya home">
            <img src="{{ asset('images/logo.png') }}" alt="">
        </a>

        <button
            type="button"
            x-ref="railToggle"
            @click="openMenu()"
            :aria-expanded="open.toString()"
            aria-controls="aanaya-navigation-drawer"
            aria-label="Open navigation menu"
            class="aanaya-rail-toggle">
            <span></span><span></span>
        </button>

        <div class="aanaya-rail-links">
            @foreach($navigationItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="aanaya-rail-link {{ $item['active'] ? 'is-active' : '' }}"
                    aria-label="{{ $item['label'] }}"
                    @if($item['active']) aria-current="page" @endif>
                    <i class="fas {{ $item['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="aanaya-rail-utilities">
            @auth
                <a href="{{ route('cart.index') }}" class="aanaya-rail-link" aria-label="Cart{{ $navbarCartCount ? ', '.$navbarCartCount.' items' : '' }}">
                    <i class="fas fa-bag-shopping" aria-hidden="true"></i>
                    @if($navbarCartCount > 0)<b>{{ min($navbarCartCount, 99) }}</b>@endif
                    <span>Cart</span>
                </a>
                <button type="button" @click="openMenu()" class="aanaya-rail-profile" aria-label="Open account navigation">
                    <span>{{ $navbarInitials ?: 'A' }}</span>
                    @if($hasNavbarAvatar)
                        <img src="{{ $navbarAvatarUrl }}" alt="" x-on:error="$el.remove()">
                    @endif
                </button>
            @else
                <a href="{{ route('login') }}" class="aanaya-rail-link" aria-label="Login">
                    <i class="fas fa-user" aria-hidden="true"></i><span>Login</span>
                </a>
            @endauth
        </div>
    </aside>

    <header class="aanaya-mobile-header">
        <a href="{{ route('dashboard') }}" class="aanaya-mobile-logo" aria-label="Aanaya home">
            <img src="{{ asset('images/logo.png') }}" alt="">
        </a>
        <span class="aanaya-mobile-page">
            {{ collect($navigationItems)->firstWhere('active', true)['label'] ?? 'Aanaya' }}
        </span>
        <button
            type="button"
            x-ref="mobileToggle"
            @click="openMenu()"
            :aria-expanded="open.toString()"
            aria-controls="aanaya-navigation-drawer"
            aria-label="Open navigation menu"
            class="aanaya-mobile-toggle">
            <span></span><span></span>
        </button>
    </header>

    <template x-teleport="body">
        <div x-cloak x-show="open" class="aanaya-drawer-layer">
            <button
                type="button"
                x-show="open"
                x-transition.opacity.duration.200ms
                @click="closeMenu(true)"
                class="aanaya-drawer-scrim"
                aria-label="Close navigation menu"></button>

            <aside
                id="aanaya-navigation-drawer"
                x-ref="drawer"
                x-show="open"
                @keydown.tab="trapFocus($event)"
                x-transition:enter="aanaya-drawer-transition"
                x-transition:enter-start="aanaya-drawer-closed"
                x-transition:enter-end="aanaya-drawer-open"
                x-transition:leave="aanaya-drawer-transition"
                x-transition:leave-start="aanaya-drawer-open"
                x-transition:leave-end="aanaya-drawer-closed"
                class="aanaya-drawer"
                role="dialog"
                aria-modal="true"
                aria-labelledby="aanaya-drawer-title">

                <div class="aanaya-drawer-atmosphere" aria-hidden="true"></div>
                <header class="aanaya-drawer-header">
                    <a href="{{ route('dashboard') }}" class="aanaya-drawer-brand" @click="closeMenu()">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                        <span><small>Step inside</small><strong id="aanaya-drawer-title">Aanaya Universe</strong></span>
                    </a>
                    <button type="button" x-ref="drawerClose" @click="closeMenu(true)" class="aanaya-drawer-close" aria-label="Close navigation menu">
                        <i class="fas fa-xmark" aria-hidden="true"></i>
                    </button>
                </header>

                <nav class="aanaya-drawer-nav" aria-label="Explore Aanaya">
                    @foreach($navigationItems as $item)
                        <a
                            href="{{ route($item['route']) }}"
                            class="{{ $item['active'] ? 'is-active' : '' }}"
                            @if($item['active']) aria-current="page" @endif
                            @click="closeMenu()">
                            <span class="aanaya-drawer-number">{{ $item['number'] }}</span>
                            <span class="aanaya-drawer-label">{{ $item['label'] }}</span>
                            <i class="fas fa-arrow-right-long" aria-hidden="true"></i>
                        </a>
                    @endforeach
                </nav>

                <div class="aanaya-drawer-account">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="aanaya-drawer-profile" @click="closeMenu()">
                            <span class="aanaya-drawer-avatar" aria-hidden="true">
                                <span>{{ $navbarInitials ?: 'A' }}</span>
                                @if($hasNavbarAvatar)<img src="{{ $navbarAvatarUrl }}" alt="" x-on:error="$el.remove()">@endif
                            </span>
                            <span class="aanaya-drawer-user-copy">
                                <small>Signed in as</small><strong>{{ $navbarUser->name }}</strong>
                            </span>
                        </a>
                        <div class="aanaya-drawer-utility-links">
                            <a href="{{ route('cart.index') }}" @click="closeMenu()">Cart @if($navbarCartCount > 0)<span>{{ $navbarCartCount }}</span>@endif</a>
                            <a href="{{ route('orders.index') }}" @click="closeMenu()">Orders</a>
                            @if($navbarUser->role === 'admin')<a href="/admin" @click="closeMenu()">Admin</a>@endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="aanaya-drawer-logout">Logout <i class="fas fa-arrow-right-from-bracket" aria-hidden="true"></i></button>
                        </form>
                    @else
                        <p>Keep a little piece of the Aanaya universe with you.</p>
                        <div class="aanaya-drawer-guest-actions">
                            <a href="{{ route('login') }}" @click="closeMenu()">Login</a>
                            <a href="{{ route('register') }}" @click="closeMenu()">Create account</a>
                        </div>
                    @endauth
                </div>
            </aside>
        </div>
    </template>
</nav>
