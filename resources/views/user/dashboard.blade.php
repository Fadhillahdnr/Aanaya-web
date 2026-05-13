<x-app-layout>

    <div class="user-dashboard">

        <!-- BACKGROUND GLOW -->
        <div class="dashboard-bg-glow glow-1"></div>
        <div class="dashboard-bg-glow glow-2"></div>

        <!-- HERO -->
        <section class="user-hero">

            <!-- LEFT -->
            <div class="hero-left">

                <span class="hero-badge">
                    ✨ DREAMY • CINEMATIC • EMOTIONAL
                </span>

                <h1>
                    Welcome Back,
                    <span>{{ Auth::user()->name }}</span>
                </h1>

                <p class="hero-description">
                    Dive into the dreamy universe of Aanaya —
                    stream emotional music, explore cinematic visuals,
                    discover exclusive merchandise,
                    and feel the atmosphere behind every release.
                </p>

                <!-- BUTTON -->
                <div class="hero-buttons">

                    <a href="/music" class="hero-btn primary-btn">

                        <i class="fas fa-play"></i>

                        Listen Now

                    </a>

                    <a href="/gallery" class="hero-btn secondary-btn">

                        <i class="fas fa-image"></i>

                        Explore Gallery

                    </a>

                </div>

                <!-- STATS -->
                <div class="hero-stats">

                    <div class="hero-stat-card">

                        <h2>{{ $totalMusic }}</h2>

                        <p>Music</p>

                    </div>

                    <div class="hero-stat-card">

                        <h2>{{ $totalArticles }}</h2>

                        <p>Articles</p>

                    </div>

                    <div class="hero-stat-card">

                        <h2>{{ $totalProducts }}</h2>

                        <p>Products</p>

                    </div>

                    <div class="hero-stat-card">

                        <h2>{{ $totalGallery }}</h2>

                        <p>Gallery</p>

                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="dashboard-hero-card">

                <div class="hero-glow"></div>

                <div class="latest-label">

                    <i class="fas fa-fire"></i>

                    Latest Release

                </div>

                @if(isset($latestMusic) && $latestMusic)

                    <!-- COVER -->
                    <div class="latest-cover-wrapper">

                        <img
                            src="{{ asset($latestMusic->cover_image) }}"
                            alt="{{ $latestMusic->title }}"
                            class="latest-cover">

                    </div>

                    <!-- INFO -->
                    <div class="latest-info">

                        <span class="latest-category">
                            NEW MUSIC
                        </span>

                        <h2>
                            {{ $latestMusic->title }}
                        </h2>

                        <p>
                            {{ $latestMusic->artist }}
                        </p>

                    </div>

                    <!-- AUDIO -->
                    <div class="latest-audio">

                        <audio controls>

                            <source
                                src="{{ asset($latestMusic->audio_file) }}"
                                type="audio/mpeg">

                        </audio>

                    </div>

                @else

                    <div class="empty-release">

                        <i class="fas fa-music"></i>

                        <h3>No Music Yet</h3>

                        <p>
                            Latest releases will appear here.
                        </p>

                    </div>

                @endif

            </div>

        </section>

        <!-- QUICK ACCESS -->
        <section class="dashboard-section">

            <div class="section-title">

                <span>DISCOVER</span>

                <h2>Explore The Universe</h2>

                <p>
                    Everything you need in one dreamy place.
                </p>

            </div>

            <div class="quick-grid">

                <!-- MUSIC -->
                <a href="/music" class="quick-card">

                    <div class="quick-icon pink">

                        <i class="fas fa-music"></i>

                    </div>

                    <div class="quick-content">

                        <h3>Music</h3>

                        <p>
                            Listen to emotional cinematic tracks.
                        </p>

                    </div>

                    <span class="quick-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>

                </a>

                <!-- ARTICLES -->
                <a href="/articles" class="quick-card">

                    <div class="quick-icon purple">

                        <i class="fas fa-newspaper"></i>

                    </div>

                    <div class="quick-content">

                        <h3>Articles</h3>

                        <p>
                            Read stories and latest updates.
                        </p>

                    </div>

                    <span class="quick-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>

                </a>

                <!-- GALLERY -->
                <a href="/gallery" class="quick-card">

                    <div class="quick-icon blue">

                        <i class="fas fa-camera"></i>

                    </div>

                    <div class="quick-content">

                        <h3>Gallery</h3>

                        <p>
                            Explore aesthetic dreamy visuals.
                        </p>

                    </div>

                    <span class="quick-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>

                </a>

                <!-- PRODUCT -->
                <a href="/products" class="quick-card">

                    <div class="quick-icon peach">

                        <i class="fas fa-bag-shopping"></i>

                    </div>

                    <div class="quick-content">

                        <h3>Merchandise</h3>

                        <p>
                            Official Aanaya merchandise collection.
                        </p>

                    </div>

                    <span class="quick-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </span>

                </a>

            </div>

        </section>

        <!-- RECENT MUSIC -->
        <section class="dashboard-section">

            <div class="section-title">

                <span>LATEST MUSIC</span>

                <h2>Recent Releases</h2>

            </div>

            <div class="recent-music-grid">

                @forelse($recentMusics as $music)

                    <div class="music-card">

                        <div class="music-image-wrapper">

                            <img
                                src="{{ asset($music->cover_image) }}"
                                alt="{{ $music->title }}">

                        </div>

                        <div class="music-content">

                            <h3>
                                {{ $music->title }}
                            </h3>

                            <p>
                                {{ $music->artist }}
                            </p>

                            <audio controls>

                                <source
                                    src="{{ asset($music->audio_file) }}"
                                    type="audio/mpeg">

                            </audio>

                        </div>

                    </div>

                @empty

                    <div class="empty-box">

                        <i class="fas fa-music"></i>

                        <p>No music uploaded yet</p>

                    </div>

                @endforelse

            </div>

        </section>

        <!-- QUOTE -->
        <section class="quote-section">

            <div class="quote-card">

                <div class="quote-glow"></div>

                <i class="fas fa-quote-left"></i>

                <h2>
                    “Music gives a soul to the universe,
                    wings to the mind,
                    and life to everything.”
                </h2>

                <span>
                    — Aanaya Universe
                </span>

            </div>

        </section>

    </div>

</x-app-layout>