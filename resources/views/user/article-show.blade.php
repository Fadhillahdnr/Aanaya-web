<x-app-layout>

<div class="article-detail-page">

    <div class="article-detail-bg glow-1"></div>
    <div class="article-detail-bg glow-2"></div>
    <div class="article-detail-bg glow-3"></div>

    <div class="article-container">

        {{-- ===================================================== --}}
        {{-- HERO --}}
        {{-- ===================================================== --}}

        <section class="article-hero">

            <div class="article-hero-image">

                <img
                    src="{{ $article->thumbnail }}"
                    alt="{{ $article->title }}">

            </div>

            <div class="article-hero-content">

                <span class="article-category-badge">
                    {{ strtoupper($article->category) }}
                </span>

                <h1>
                    {{ $article->title }}
                </h1>

                <div class="article-meta">

                    <span>
                        <i class="fas fa-user"></i>
                        {{ $article->author->name }}
                    </span>

                    <span>
                        <i class="fas fa-calendar"></i>
                        {{ $article->created_at->format('d M Y') }}
                    </span>

                    <span>
                        <i class="fas fa-clock"></i>
                        {{ $article->created_at->diffForHumans() }}
                    </span>

                </div>

            </div>

        </section>

        @include('user.article-blok')

        {{-- ===================================================== --}}
        {{-- FOOTER --}}
        {{-- ===================================================== --}}

        <div class="article-footer-action">

            <a
                href="{{ route('articles') }}"
                class="article-back-btn">

                <i class="fas fa-arrow-left"></i>

                Back to Articles

            </a>

        </div>

    </div>

</div>

</x-app-layout>