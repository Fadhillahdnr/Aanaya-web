<nav x-data="{ open: false }" class="admin-navbar">

    <div class="admin-container">

        <!-- ===================================================== -->
        <!-- LEFT -->
        <!-- ===================================================== -->

        <div class="admin-left">

            <!-- LOGO -->
            <a href="/admin" class="admin-logo-link">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Aanaya Logo"
                    class="admin-logo">

            </a>

            <!-- ===================================================== -->
            <!-- DESKTOP LINKS -->
            <!-- ===================================================== -->

            <div class="admin-links-desktop">

                <!-- DASHBOARD -->
                <a
                    href="/admin"
                    class="{{ request()->is('admin') ? 'active' : '' }}">

                    <i class="fas fa-chart-line"></i>

                    <span>Dashboard</span>

                </a>

                <!-- MUSIC -->
                <a
                    href="/admin/music"
                    class="{{ request()->is('admin/music*') ? 'active' : '' }}">

                    <i class="fas fa-music"></i>

                    <span>Music</span>

                </a>

                <!-- ARTICLES -->
                <a
                    href="/admin/articles"
                    class="{{ request()->is('admin/articles*') ? 'active' : '' }}">

                    <i class="fas fa-newspaper"></i>

                    <span>Articles</span>

                </a>

                <!-- PRODUCTS -->
                <a
                    href="/admin/products"
                    class="{{ request()->is('admin/products*') ? 'active' : '' }}">

                    <i class="fas fa-bag-shopping"></i>

                    <span>Products</span>

                </a>

                <!-- GALLERY -->
                <a
                    href="/admin/gallery"
                    class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">

                    <i class="fas fa-image"></i>

                    <span>Gallery</span>

                </a>

                <!-- USERS -->
                <a
                    href="/admin/users"
                    class="{{ request()->is('admin/users*') ? 'active' : '' }}">

                    <i class="fas fa-users"></i>

                    <span>Users</span>

                </a>

            </div>

        </div>

        <!-- ===================================================== -->
        <!-- RIGHT -->
        <!-- ===================================================== -->

        <div class="admin-right">

            <!-- USER -->
            <div class="admin-user">

                <div class="admin-avatar">

                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}

                </div>

                <div class="admin-user-info">

                    <h4>
                        {{ Auth::user()->name }}
                    </h4>

                    <p>
                        Administrator
                    </p>

                </div>

            </div>

            <!-- ===================================================== -->
            <!-- LOGOUT -->
            <!-- ===================================================== -->

            <form
                method="POST"
                action="{{ route('logout') }}">

                @csrf

                <button
                    type="submit"
                    class="logout-btn"
                    title="Logout">

                    <i class="fas fa-right-from-bracket"></i>

                </button>

            </form>

            <!-- ===================================================== -->
            <!-- MOBILE TOGGLE -->
            <!-- ===================================================== -->

            <button
                @click="open = ! open"
                class="admin-mobile-toggle"
                title="Toggle menu">

                <i class="fas fa-bars"></i>

            </button>

        </div>

    </div>

    <!-- ===================================================== -->
    <!-- MOBILE MENU -->
    <!-- ===================================================== -->

    <div
        x-show="open"
        x-transition
        class="admin-links-mobile">

        <!-- DASHBOARD -->
        <a
            href="/admin"
            class="{{ request()->is('admin') ? 'active' : '' }}">

            <i class="fas fa-chart-line"></i>

            Dashboard

        </a>

        <!-- MUSIC -->
        <a
            href="/admin/music"
            class="{{ request()->is('admin/music*') ? 'active' : '' }}">

            <i class="fas fa-music"></i>

            Music

        </a>

        <!-- ARTICLES -->
        <a
            href="/admin/articles"
            class="{{ request()->is('admin/articles*') ? 'active' : '' }}">

            <i class="fas fa-newspaper"></i>

            Articles

        </a>

        <!-- PRODUCTS -->
        <a
            href="/admin/products"
            class="{{ request()->is('admin/products*') ? 'active' : '' }}">

            <i class="fas fa-bag-shopping"></i>

            Products

        </a>

        <!-- GALLERY -->
        <a
            href="/admin/gallery"
            class="{{ request()->is('admin/gallery*') ? 'active' : '' }}">

            <i class="fas fa-image"></i>

            Gallery

        </a>

        <!-- USERS -->
        <a
            href="/admin/users"
            class="{{ request()->is('admin/users*') ? 'active' : '' }}">

            <i class="fas fa-users"></i>

            Users
        </a>
        
    </div>

</nav>