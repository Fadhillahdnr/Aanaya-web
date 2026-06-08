<x-app-layout>

<div class="user-articles-page">

    <!-- BG -->
    <div class="articles-bg glow-1"></div>
    <div class="articles-bg glow-2"></div>

    <!-- HERO -->
    <section class="articles-hero">

        <div class="articles-hero-content">

            <span class="articles-badge">
                <span class="badge-dot"></span>
                Aanaya Stories
            </span>

            <h1>
                Aanaya
                <span>Articles</span>
            </h1>

            <p>
                Dive into emotional stories,
                behind-the-scenes moments,
                dreamy inspirations,
                and cinematic journeys from Aanaya universe.
            </p>

        </div>

    </section>

    <!-- FEATURED -->
    @if($articles->count())

    @php
        $featured = $articles->first();
    @endphp

    <section class="featured-article">

        <!-- IMAGE -->
        <div class="featured-image">

                <img src="{{ $featured->thumbnail }}"
                    alt="{{ $featured->title }}">
    
                <div class="featured-overlay"></div>

        </div>

        <!-- CONTENT -->
        <div class="featured-content">

            <span class="featured-tag">
                Featured Story
            </span>

            <h2>
                {{ $featured->title }}
            </h2>

            <p>
                {{ Str::limit(strip_tags($featured->content), 220) }}
            </p>

            <div class="featured-meta">

                <span>
                    <i class="fas fa-user"></i>

                    {{ $featured->author->name }}
                </span>

                <span>
                    <i class="fas fa-calendar"></i>

                    {{ $featured->created_at->format('d M Y') }}
                </span>

            </div>

            <a href="{{ route('articles.show', $featured->slug) }}"
               class="featured-btn">

                Read Full Story

                <i class="fas fa-arrow-right"></i>

            </a>

        </div>

    </section>

    @endif

    <!-- GRID -->
    <section class="articles-grid">

        @forelse($articles as $article)

        <div class="article-card">

            <!-- IMAGE -->
            <div class="article-image">

                <img
                        src="{{ $article->thumbnail }}"
                        alt="{{ $article->title }}">

                <div class="article-overlay"></div>

            </div>

            <!-- BODY -->
            <div class="article-body">

                <div class="article-meta">

                    <span>

                        <i class="fas fa-user"></i>

                        {{ $article->author->name }}

                    </span>

                    <span>

                        <i class="fas fa-clock"></i>

                        {{ $article->created_at->diffForHumans() }}

                    </span>

                </div>

                <h2>
                    {{ $article->title }}
                </h2>

                <p>
                    {{ Str::limit(strip_tags($article->content), 120) }}
                </p>

                <a href="{{ route('articles.show', $article->slug) }}"
                   class="featured-btn">

                    Read More

                    <i class="fas fa-arrow-right"></i>

                </a>

            </div>

        </div>

        @empty

        <div class="empty-articles">

            <i class="fas fa-newspaper"></i>

            <h2>
                No Articles Yet
            </h2>

            <p>
                New dreamy stories from Aanaya
                will appear soon ✨
            </p>

        </div>

        @endforelse

    </section>

</div>

</x-app-layout>