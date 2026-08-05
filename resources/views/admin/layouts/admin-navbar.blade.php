<nav
    x-data="{
        open: false,
        openMenu() {
            this.open = true;
            this.$nextTick(() => this.$refs.adminMobileClose?.focus());
        },
        closeMenu(returnFocus = false) {
            this.open = false;
            if (returnFocus) this.$nextTick(() => this.$refs.adminMobileToggle?.focus());
        }
    }"
    x-effect="document.body.classList.toggle('admin-navigation-open', open)"
    @keydown.escape.window="open && closeMenu(true)"
    @resize.window="if (window.innerWidth > 1024 && open) closeMenu()"
    class="admin-navbar"
    aria-label="Admin navigation">

    <div class="admin-container">

        {{-- =========================================
             LOGO
        ========================================== --}}
        <div class="admin-left">

            <a href="/admin"
               class="admin-logo-link"
               title="Dashboard">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Aanaya Logo"
                    class="admin-logo">

            </a>

        </div>

        {{-- =========================================
             CENTER MENU
        ========================================== --}}
        <div class="admin-nav-center">

            <a href="/admin"
               class="{{ request()->is('admin') ? 'active' : '' }}">

                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>

            </a>

            {{-- Upload Dropdown --}}
            <div
                x-data="{ uploadOpen:false }"
                class="admin-dropdown">

                <button
                    type="button"
                    @click="uploadOpen = !uploadOpen"
                    :aria-expanded="uploadOpen.toString()"
                    aria-haspopup="true"
                    class="admin-dropdown-btn">

                    <i class="fas fa-cloud-arrow-up"></i>

                    <span>Upload</span>

                    <i
                        class="fas fa-chevron-down dropdown-arrow"
                        :class="{ 'rotate': uploadOpen }">
                    </i>

                </button>

                <div
                    x-show="uploadOpen"
                    @click.outside="uploadOpen = false"

                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"

                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"

                    class="admin-dropdown-menu"
                    style="display:none;">

                    <a href="/admin/music">
                        <i class="fas fa-music"></i>
                        Music
                    </a>

                    <a href="/admin/articles">
                        <i class="fas fa-newspaper"></i>
                        Articles
                    </a>

                    <a href="/admin/products">
                        <i class="fas fa-bag-shopping"></i>
                        Products
                    </a>

                    <a href="/admin/gallery">
                        <i class="fas fa-image"></i>
                        Gallery
                    </a>

                    <a href="/admin/mv">
                        <i class="fas fa-video"></i>
                        Videos
                    </a>

                </div>

            </div>

            <a href="/admin/users"
               class="{{ request()->is('admin/users*') ? 'active' : '' }}">

                <i class="fas fa-users"></i>
                <span>Users</span>

            </a>

            <a href="/admin/orders"
               class="{{ request()->is('admin/orders*') ? 'active' : '' }}">

                <i class="fas fa-receipt"></i>
                <span>Orders</span>

            </a>

        </div>

        {{-- =========================================
             RIGHT SIDE
        ========================================== --}}
        <div class="admin-right">

            <div class="admin-user">

                <div class="admin-avatar">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A',0,1)) }}
                </div>

                <div class="admin-user-info">

                    <h4>
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </h4>

                    <p>
                        Administrator
                    </p>

                </div>

            </div>

            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                    aria-label="Log out"
                    title="Log out">

                    <i class="fas fa-right-from-bracket"></i>

                </button>

            </form>

            {{-- MOBILE BUTTON --}}
            <button
                type="button"
                x-ref="adminMobileToggle"
                @click="open ? closeMenu() : openMenu()"
                class="admin-mobile-toggle"
                :class="{ 'is-open': open }"
                :aria-expanded="open.toString()"
                aria-controls="admin-mobile-sidebar"
                aria-label="Open admin navigation">

                <i class="fas fa-bars"></i>

            </button>

        </div>

    </div>

    <template x-teleport="body">
        <div x-cloak x-show="open" class="admin-drawer-layer" aria-labelledby="admin-mobile-sidebar-title">
            <button
                type="button"
                x-show="open"
                x-transition.opacity.duration.200ms
                @click="closeMenu(true)"
                class="admin-drawer-scrim"
                aria-label="Close admin navigation">
            </button>

            <aside
                id="admin-mobile-sidebar"
                x-show="open"
                x-transition:enter="admin-drawer-enter"
                x-transition:enter-start="admin-drawer-enter-start"
                x-transition:enter-end="admin-drawer-enter-end"
                x-transition:leave="admin-drawer-leave"
                x-transition:leave-start="admin-drawer-leave-start"
                x-transition:leave-end="admin-drawer-leave-end"
                @click.outside="closeMenu()"
                class="admin-mobile-drawer"
                role="dialog"
                aria-modal="true">

                <div class="admin-drawer-header">
                    <a href="/admin" class="admin-drawer-brand" @click="closeMenu()">
                        <img src="{{ asset('images/logo.png') }}" alt="">
                        <span>
                            <small>Workspace</small>
                            <strong id="admin-mobile-sidebar-title">Aanaya Admin</strong>
                        </span>
                    </a>
                    <button
                        type="button"
                        x-ref="adminMobileClose"
                        @click="closeMenu(true)"
                        class="admin-drawer-close"
                        aria-label="Close admin navigation">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <div class="admin-drawer-profile">
                    <span class="admin-drawer-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}</span>
                    <span>
                        <strong>{{ Auth::user()->name ?? 'Administrator' }}</strong>
                        <small>Administrator</small>
                    </span>
                </div>

                <nav class="admin-drawer-nav" aria-label="Admin mobile navigation">
                    <span class="admin-drawer-label">Overview</span>
                    <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-chart-line"></i><span>Dashboard</span>
                    </a>

                    <span class="admin-drawer-label admin-drawer-label-manage">Manage content</span>
                    <a href="/admin/music" class="{{ request()->is('admin/music*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-music"></i><span>Music</span>
                    </a>
                    <a href="/admin/articles" class="{{ request()->is('admin/articles*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-newspaper"></i><span>Articles</span>
                    </a>
                    <a href="/admin/products" class="{{ request()->is('admin/products*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-bag-shopping"></i><span>Products</span>
                    </a>
                    <a href="/admin/gallery" class="{{ request()->is('admin/gallery*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-image"></i><span>Gallery</span>
                    </a>
                    <a href="/admin/mv" class="{{ request()->is('admin/mv*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-video"></i><span>Videos</span>
                    </a>

                    <span class="admin-drawer-label admin-drawer-label-manage">Operations</span>
                    <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-users"></i><span>Users</span>
                    </a>
                    <a href="/admin/orders" class="{{ request()->is('admin/orders*') ? 'active' : '' }}" @click="closeMenu()">
                        <i class="fas fa-receipt"></i><span>Orders</span>
                    </a>
                </nav>

                <form method="POST" action="{{ route('logout') }}" class="admin-drawer-footer">
                    @csrf
                    <button type="submit" class="admin-drawer-logout">
                        <i class="fas fa-right-from-bracket"></i><span>Logout</span>
                    </button>
                </form>
            </aside>
        </div>
    </template>

</nav>
