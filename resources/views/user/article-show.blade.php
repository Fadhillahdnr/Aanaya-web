<x-app-layout>

<div class="article-detail-page">

    <!-- BACKGROUND -->
    <div class="article-detail-bg glow-1"></div>
    <div class="article-detail-bg glow-2"></div>

    <!-- HERO -->
    <section class="article-detail-hero">

        <!-- IMAGE -->
        <div class="article-detail-image-wrap">

            <img
                src="{{ asset('uploads/articles/' . $article->thumbnail) }}"
                alt="{{ $article->title }}"
                class="article-detail-image"
            >

            <div class="article-detail-overlay"></div>

        </div>

        <!-- CONTENT -->
        <div class="article-detail-content">

            <span class="article-detail-badge">
                ✨ DREAMY STORY
            </span>

            <h1>
                {{ $article->title }}
            </h1>

            <div class="article-detail-meta">

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

    <!-- STORY -->
    <section class="article-story-section">

        <div class="article-story-card">

            <div class="article-story-content">

                {!! $article->content !!}

            </div>

        </div>

    </section>

    <!-- BACK BUTTON -->
    <div class="article-back-wrap">

        <a href="{{ route('articles') }}"
           class="article-back-btn">

            <i class="fas fa-arrow-left"></i>

            Back to Articles

        </a>

    </div>

</div>

</x-app-layout>