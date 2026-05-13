@extends('admin.layouts.admin')

@section('content')

<div class="dashboard-wrapper">

    <!-- HERO -->
    <div class="dashboard-hero">

        <!-- LEFT -->
        <div class="dashboard-text">

            <span class="dashboard-badge">
                AANAYA ADMIN PANEL
            </span>

            <h1>
                Welcome Back,
                <span>{{ Auth::user()->name }}</span> ✨
            </h1>

            <p>
                Manage music releases, articles, merchandise,
                and the entire Aanaya universe from one place.
            </p>

        </div>

        <!-- RIGHT CARD -->
        <div class="dashboard-hero-card">

            <div class="hero-glow"></div>

            <div class="latest-label">
                <i class="fas fa-fire"></i>
                Latest Release
            </div>

            @if($latestMusic)

                <!-- COVER -->
                <div class="latest-cover-wrapper">

                    <img src="{{ asset($latestMusic->cover_image) }}" alt="{{ $latestMusic->title }}" class="latest-cover">

                </div>

                <!-- INFO -->
                <div class="latest-info">

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

                    <p>No music uploaded yet</p>

                </div>

            @endif

        </div>

    </div>

    <!-- STATS -->
    <div class="stats-grid">

        <!-- MUSIC -->
        <div class="stats-card">

            <div class="stats-top">

                <div class="stats-icon pink">
                    <i class="fas fa-music"></i>
                </div>

                <span class="stats-badge">
                    Music
                </span>

            </div>

            <div class="stats-content">

                <h2>{{ $totalMusic }}</h2>

                <p>Total Music Uploaded</p>

            </div>

            <div class="stats-wave"></div>

        </div>

        <!-- ARTICLES -->
        <div class="stats-card">

            <div class="stats-top">

                <div class="stats-icon purple">
                    <i class="fas fa-newspaper"></i>
                </div>

                <span class="stats-badge">
                    Articles
                </span>

            </div>

            <div class="stats-content">

                <h2>{{ $totalArticles }}</h2>

                <p>Published Articles</p>

            </div>

            <div class="stats-wave"></div>

        </div>

        <!-- PRODUCTS -->
        <div class="stats-card">

            <div class="stats-top">

                <div class="stats-icon peach">
                    <i class="fas fa-shirt"></i>
                </div>

                <span class="stats-badge">
                    Merchandise
                </span>

            </div>

            <div class="stats-content">

                <h2>{{ $totalProducts }}</h2>

                <p>Official Products</p>

            </div>

            <div class="stats-wave"></div>

        </div>

        <!-- GALLERY -->
        <div class="stats-card">

            <div class="stats-top">

                <div class="stats-icon blue">
                    <i class="fas fa-image"></i>
                </div>

                <span class="stats-badge">
                    Gallery
                </span>

            </div>

            <div class="stats-content">

                <h2>{{ $totalGallery }}</h2>

                <p>Uploaded Photos</p>

            </div>

            <div class="stats-wave"></div>

        </div>

    </div>

    <!-- QUICK ACTION -->
    <div class="quick-section">

        <div class="quick-title">

            <span class="quick-badge">
                QUICK ACCESS
            </span>

            <h2>
                Quick Actions
            </h2>

            <p>
                Manage your Aanaya universe faster ✨
            </p>

        </div>

        <div class="quick-grid">

            <!-- MUSIC -->
            <a href="/admin/music" class="quick-card">

                <div class="quick-icon pink-bg">
                    <i class="fas fa-plus"></i>
                </div>

                <div class="quick-content">

                    <h3>Add Music</h3>

                    <p>
                        Upload new dreamy songs & albums
                    </p>

                </div>

                <span class="quick-arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>

            </a>

            <!-- ARTICLE -->
            <a href="/admin/articles" class="quick-card">

                <div class="quick-icon purple-bg">
                    <i class="fas fa-pen"></i>
                </div>

                <div class="quick-content">

                    <h3>Create Article</h3>

                    <p>
                        Share latest news & stories
                    </p>

                </div>

                <span class="quick-arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>

            </a>

            <!-- PRODUCT -->
            <a href="/admin/products" class="quick-card">

                <div class="quick-icon peach-bg">
                    <i class="fas fa-bag-shopping"></i>
                </div>

                <div class="quick-content">

                    <h3>Add Merchandise</h3>

                    <p>
                        Manage official Aanaya products
                    </p>

                </div>

                <span class="quick-arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>

            </a>

            <!-- GALLERY -->
            <a href="/admin/gallery" class="quick-card">

                <div class="quick-icon blue-bg">
                    <i class="fas fa-camera"></i>
                </div>

                <div class="quick-content">

                    <h3>Upload Gallery</h3>

                    <p>
                        Add concert & aesthetic photos
                    </p>

                </div>

                <span class="quick-arrow">
                    <i class="fas fa-arrow-right"></i>
                </span>

            </a>

        </div>

    </div>

</div>

@endsection