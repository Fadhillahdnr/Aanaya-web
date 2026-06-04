<x-app-layout>

<div class="article-detail-page comic-vibe">

    <div class="article-detail-bg glow-1"></div>
    <div class="article-detail-bg glow-2"></div>

    <div class="comic-page-container">
        
        <section class="article-detail-hero comic-thick-border">

            <div class="article-detail-image-wrap">
                <img
                    src="{{ $article->thumbnail }}"
                    alt="{{ $article->title }}"
                    class="article-detail-image"
                >
                <div class="article-detail-overlay"></div>
                <div class="comic-corner-tag">AANAYA VOL. 01</div>
            </div>

            <div class="article-detail-content">
                <span class="article-detail-badge comic-badge">
                    💥 MSYL CHAPTER
                </span>

                <h1 class="comic-title">
                    {{ $article->title }}
                </h1>

                <div class="article-detail-meta comic-meta">
                    <span>
                        <i class="fas fa-user-ninja"></i>
                        By {{ $article->author->name }}
                    </span>
                    <span>
                        <i class="fas fa-calendar-alt"></i>
                        {{ $article->created_at->format('d M Y') }}
                    </span>
                    <span>
                        <i class="fas fa-history"></i>
                        {{ $article->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

        </section>

        <section class="article-story-section">
            <div class="article-story-card comic-paper-texture comic-thick-border">
                
                <div class="comic-dots-decor"></div>

                <div class="article-story-content comic-typography">
                    {!! $article->content !!}
                </div>
            </div>
        </section>

        <div class="article-back-wrap">
            <a href="{{ route('articles') }}" class="article-back-btn comic-btn">
                <i class="fas fa-arrow-left"></i>
                KEMBALI KE ARTIKEL
            </a>
        </div>

    </div>

</div>

</x-app-layout>