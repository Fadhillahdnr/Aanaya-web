<nav x-data="{ open: false }" class="admin-navbar">

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
                    class="logout-btn">

                    <i class="fas fa-right-from-bracket"></i>

                </button>

            </form>

            {{-- MOBILE BUTTON --}}
            <button
                @click="open = ! open"
                class="admin-mobile-toggle"
                :class="{ 'is-open': open }">

                <i
                    class="fas fa-bars"
                    x-show="!open">
                </i>

                <i
                    class="fas fa-xmark"
                    x-show="open"
                    style="display:none;">
                </i>

            </button>

        </div>

    </div>

    {{-- =========================================
         MOBILE MENU
    ========================================== --}}
    <div
        x-show="open"
        x-transition
        class="admin-links-mobile"
        style="display:none;">

        <a href="/admin">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>

        <a href="/admin/music">
            <i class="fas fa-music"></i>
            Music
        </a>

        <div
            x-data="{ uploadOpen:false }"
            class="admin-dropdown">

            <button
                type="button"
                @click="uploadOpen = !uploadOpen"
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

        <a href="/admin/users">
            <i class="fas fa-users"></i>
            Users
        </a>

        <a href="/admin/orders">
            <i class="fas fa-receipt"></i>
            Orders
        </a>

    </div>

</nav>