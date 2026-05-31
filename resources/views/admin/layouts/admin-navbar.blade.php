<nav x-data="{ open: false }" class="admin-navbar">

    <div class="admin-container">

        <div class="admin-left">

            <a href="/admin" class="admin-logo-link" title="Go to Dashboard">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Aanaya Logo"
                    class="admin-logo">
            </a>

            <div class="admin-links-desktop">

                <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/music" class="{{ request()->is('admin/music*') ? 'active' : '' }}">
                    <i class="fas fa-music"></i>
                    <span>Music</span>
                </a>

                <a href="/admin/articles" class="{{ request()->is('admin/articles*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    <span>Articles</span>
                </a>

                <a href="/admin/products" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class="fas fa-bag-shopping"></i>
                    <span>Products</span>
                </a>

                <a href="/admin/gallery" class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">
                    <i class="fas fa-image"></i>
                    <span>Gallery</span>
                </a>

                <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>

                <a href="/admin/mv" class="{{ request()->is('admin/mv*') ? 'active' : '' }}">
                    <i class="fas fa-video"></i>
                    <span>Music Videos</span>
                </a>

            </div>

        </div>

        <div class="admin-right">

            <div class="admin-user">
                <div class="admin-user-info">
                    <h4>{{ Auth::user()->name ?? 'Administrator' }}</h4>
                    <p>Administrator</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="fas fa-right-from-bracket"></i>
                </button>
            </form>

            <button
                @click="open = ! open"
                class="admin-mobile-toggle"
                :class="{ 'is-open': open }"
                title="Toggle menu"
                aria-label="Toggle Navigation">
                
                <i class="fas fa-bars" x-show="!open"></i>
                <i class="fas fa-xmark" x-show="open" style="display: none;"></i>
            </button>

        </div>

    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform scale-95 -translate-y-4"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 -translate-y-4"
        class="admin-links-mobile"
        style="display: none;">

        <a href="/admin" class="{{ request()->is('admin') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        <a href="/admin/music" class="{{ request()->is('admin/music*') ? 'active' : '' }}">
            <i class="fas fa-music"></i> Music
        </a>

        <a href="/admin/articles" class="{{ request()->is('admin/articles*') ? 'active' : '' }}">
            <i class="fas fa-newspaper"></i> Articles
        </a>

        <a href="/admin/products" class="{{ request()->is('admin/products*') ? 'active' : '' }}">
            <i class="fas fa-bag-shopping"></i> Products
        </a>

        <a href="/admin/gallery" class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">
            <i class="fas fa-image"></i> Gallery
        </a>

        <a href="/admin/users" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Users
        </a>

        <a href="/admin/mv" class="{{ request()->is('admin/mv*') ? 'active' : '' }}">
            <i class="fas fa-video"></i> Music Videos
        </a>

    </div>

</nav>