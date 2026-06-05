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

        {{-- ===================================================== --}}
        {{-- ARTICLE --}}
        {{-- ===================================================== --}}

        @if($article->category === 'article')

            @php

                $blocks = $article->blocks
                    ->sortBy('sort_order')
                    ->values();

                $firstText = null;

                foreach($blocks as $key => $block)
                {
                    if($block->type === 'text')
                    {
                        $firstText = $block;
                        unset($blocks[$key]);
                        break;
                    }
                }

                $blocks = collect($blocks)->values();

            @endphp

            {{-- INTRO FULL WIDTH --}}
            @if($firstText)

                <section class="article-intro-section">

                    <div class="article-intro-card">

                        {!! nl2br(e($firstText->content)) !!}

                    </div>

                </section>

            @endif

            {{-- ZIG ZAG CONTENT --}}
            <section class="article-zigzag-section">

                @for($i = 0; $i < $blocks->count(); $i += 2)

                    @php
                        $left = $blocks[$i] ?? null;
                        $right = $blocks[$i + 1] ?? null;
                        $reverse = floor($i / 2) % 2;
                    @endphp

                    @if($left && $right)

                        <div class="article-zigzag-row {{ $reverse ? 'reverse' : '' }}">

                            {{-- LEFT --}}
                            <div>

                                @if($left->type === 'text')

                                    <div class="article-zigzag-text">

                                        {!! nl2br(e($left->content)) !!}

                                    </div>

                                @elseif($left->type === 'image')

                                    <div class="article-zigzag-image">

                                        <img
                                            src="{{ $left->image }}"
                                            alt="Article Image">

                                    </div>

                                @endif

                            </div>

                            {{-- RIGHT --}}
                            <div>

                                @if($right->type === 'text')

                                    <div class="article-zigzag-text">

                                        {!! nl2br(e($right->content)) !!}

                                    </div>

                                @elseif($right->type === 'image')

                                    <div class="article-zigzag-image">

                                        <img
                                            src="{{ $right->image }}"
                                            alt="Article Image">

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endif

                @endfor

            </section>

        @endif

        {{-- ===================================================== --}}
        {{-- COMIC --}}
        {{-- ===================================================== --}}

        @if($article->category === 'comic')

            <section class="comic-reader-section">

                @if($article->description)

                    <div class="comic-description">

                        {!! nl2br(e($article->description)) !!}

                    </div>

                @endif

                <div class="comic-panels">

                    @foreach(
                        $article->comicImages
                            ->sortBy('sort_order')
                        as $comic
                    )

                        <div class="comic-panel">

                            <img
                                src="{{ $comic->image }}"
                                alt="Comic Panel">

                        </div>

                    @endforeach

                </div>

            </section>

        @endif

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