<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Enter the Aanaya universe — dreamy music, emotional stories, cinematic visuals, and thoughtful keepsakes.">
    <meta name="theme-color" content="#16080e">
    <title>Aanaya — A Dream in Motion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;1,400;1,500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="welcome-body">
<main class="welcome-experience" data-welcome-experience>
    <div class="welcome-grain" aria-hidden="true"></div>

    <nav class="welcome-nav" aria-label="Welcome navigation">
        <a href="{{ route('home') }}" class="welcome-nav__brand" aria-label="Aanaya home">
            <img src="{{ asset('images/logo.png') }}" alt="">
            <span>Aanaya</span>
        </a>
        <div class="welcome-nav__links">
            @auth
                <a href="{{ route('dashboard') }}">Enter</a>
            @else
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    </nav>

    <section class="welcome-hero" data-welcome-hero aria-labelledby="welcome-title">
        <div class="welcome-hero__sticky">
            <div class="welcome-atmosphere" aria-hidden="true">
                <span class="welcome-glow welcome-glow--one" data-depth=".08"></span>
                <span class="welcome-glow welcome-glow--two" data-depth=".15"></span>
                <span class="welcome-orbit welcome-orbit--one" data-depth=".2"></span>
                <span class="welcome-orbit welcome-orbit--two" data-depth=".34"></span>
            </div>

            <canvas class="welcome-particle-canvas" data-particle-canvas aria-hidden="true" style="display: block"></canvas>

            <div class="welcome-hero__content">
                <p class="welcome-hero__eyebrow" data-intro-copy>Indonesian dream pop · a universe in bloom</p>
                <h1 id="welcome-title" class="welcome-title">AANAYA</h1>
                <p class="welcome-hero__tagline" data-intro-copy>A dream in motion.</p>
                <a href="{{ route('dashboard') }}" class="welcome-enter-link" data-magnetic data-cursor="Enter" data-intro-copy>
                    <span>Enter Aanaya</span><i aria-hidden="true">↘</i>
                </a>
            </div>

            <div class="welcome-scroll-cue" data-intro-copy aria-hidden="true"><span>Scroll to unfold</span><i></i></div>
        </div>
    </section>

    <section class="welcome-manifesto" data-welcome-manifesto aria-labelledby="manifesto-title">
        <p class="welcome-section-index">01 · Manifesto</p>
        <h2 id="manifesto-title">
            <span data-manifesto-word>We create</span>
            <span data-manifesto-word>what words</span>
            <span data-manifesto-word>cannot <em>hold.</em></span>
        </h2>
        <p class="welcome-manifesto__note">Songs become memories. Feelings become worlds. Every quiet thing is allowed to take form here.</p>
    </section>

    <section class="welcome-portals" data-welcome-portals aria-labelledby="portals-title">
        <header class="welcome-portals__header">
            <div><p class="welcome-section-index">02 · The universe</p><h2 id="portals-title">Choose where<br>the dream begins.</h2></div>
            <p>Four doors. One emotional universe.</p>
        </header>

        <div class="welcome-portal-preview" data-portal-preview aria-hidden="true">
            <img src="{{ asset('assets/visual/visual1.webp') }}" alt="" data-portal-preview-image>
        </div>

        <nav class="welcome-portal-list" aria-label="Explore Aanaya universe">
            <a href="{{ route('music') }}" data-portal-image="{{ asset('assets/visual/visual1.webp') }}" data-cursor="Listen"><span>01</span><strong>Music</strong><small>Listen</small><i>↗</i></a>
            <a href="{{ route('articles') }}" data-portal-image="{{ asset('assets/visual/visual2.webp') }}" data-cursor="Read"><span>02</span><strong>Stories</strong><small>Read</small><i>↗</i></a>
            <a href="{{ route('gallery') }}" data-portal-image="{{ asset('assets/visual/visual3.webp') }}" data-cursor="See"><span>03</span><strong>Visuals</strong><small>See</small><i>↗</i></a>
            <a href="{{ route('merchandise') }}" data-portal-image="{{ asset('assets/visual/visual4.webp') }}" data-cursor="Explore"><span>04</span><strong>Store</strong><small>Explore</small><i>↗</i></a>
        </nav>
    </section>

    <section class="welcome-final" aria-labelledby="welcome-final-title">
        <div class="welcome-final__halo" aria-hidden="true"></div>
        <p class="welcome-section-index">03 · The first page</p>
        <h2 id="welcome-final-title">Stay awhile.<br><em>Feel everything.</em></h2>
        <p>The Aanaya universe is ready when you are.</p>
        <a href="{{ route('dashboard') }}" class="welcome-final__cta" data-magnetic data-cursor="Enter">Enter the dream <span>→</span></a>
        @guest
            <div class="welcome-final__auth"><a href="{{ route('login') }}">Already belong here? Log in</a><a href="{{ route('register') }}">Create an account</a></div>
        @endguest
    </section>

    <div class="welcome-cursor" data-welcome-cursor aria-hidden="true"><span data-cursor-label>Move</span></div>
    <p class="welcome-status" data-welcome-status role="status" aria-live="polite"></p>
</main>
</body>
</html>
