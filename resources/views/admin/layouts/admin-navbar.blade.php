<nav x-data="{ open: false }" class="admin-navbar">

    <div class="admin-container">

        <!-- LEFT -->
        <div class="admin-left">

            <!-- LOGO -->
            <a href="/admin" class="admin-logo-link">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Aanaya Logo"
                     class="admin-logo">
            </a>

            <!-- DESKTOP LINKS -->
            <div class="admin-links-desktop">

                <a href="/admin">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>

                <a href="/admin/music">
                    <i class="fas fa-music"></i>
                    <span>Music</span>
                </a>

                <a href="/admin/articles">
                    <i class="fas fa-newspaper"></i>
                    <span>Articles</span>
                </a>

                <a href="/admin/products">
                    <i class="fas fa-bag-shopping"></i>
                    <span>Products</span>
                </a>

                <a href="/admin/gallery">
                    <i class="fas fa-image"></i>
                    <span>Gallery</span>
                </a>

            </div>

        </div>

        <!-- RIGHT -->
        <div class="admin-right">

            <div class="admin-user">

                <div class="admin-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <div class="admin-user-info">

                    <h4>{{ Auth::user()->name }}</h4>

                    <p>Administrator</p>

                </div>

            </div>

            <!-- LOGOUT -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="logout-btn" title="Logout">

                    <i class="fas fa-right-from-bracket"></i>

                </button>

            </form>

            <!-- MOBILE TOGGLE -->
            <button
                @click="open = ! open"
                class="admin-mobile-toggle"
                title="Toggle menu">

                <i class="fas fa-bars"></i>

            </button>

        </div>

    </div>

    <!-- MOBILE MENU -->
    <div
        x-show="open"
        x-transition
        class="admin-links-mobile">

        <a href="/admin">
            <i class="fas fa-chart-line"></i>
            Dashboard
        </a>

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

    </div>

</nav>