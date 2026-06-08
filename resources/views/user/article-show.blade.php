<x-app-layout>

<div class="article-detail-page">

    <!-- BACKGROUND -->
    <div class="article-detail-bg glow-1"></div>
    <div class="article-detail-bg glow-2"></div>
    <div class="article-detail-bg glow-3"></div>

    <div class="article-container">

        <!-- HERO -->
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

                <!-- ACTIONS -->
                <div class="comic-actions">

                    <a href="{{ url()->previous() }}"
                    class="back-btn">

                        <i class="fas fa-arrow-left"></i>

                        Back

                    </a>

                    <a href="#articleReader"
                    class="start-reading-btn">

                        <i class="fas fa-book-reader"></i>

                        Start Reading

                    </a>

                </div>

            </div>

        </section>

        {{-- ========================================= --}}
        {{-- ARTICLE MODE --}}
        {{-- ========================================= --}}

        @if(strtolower($article->category) === 'article')

            @php

                $blocks = $article->blocks
                    ->sortBy('sort_order')
                    ->values();

                $firstText = null;

                foreach($blocks as $key => $block){

                    if($block->type === 'text'){

                        $firstText = $block;

                        unset($blocks[$key]);

                        break;
                    }
                }

                $blocks = collect($blocks)->values();

            @endphp

            <section class="article-3d-wrapper" id="articleReader">

                <!-- INTRO -->

                @if($firstText)

                    <div class="article-intro-card depth-card">

                        <span class="article-mini-badge">

                            STORY EXPERIENCE

                        </span>

                        <h2>

                            Enter The World Of
                            {{ $article->title }}

                        </h2>

                        <div class="article-intro-content">

                            {!! nl2br(e($firstText->content)) !!}

                        </div>

                    </div>

                @endif

                <!-- BLOCKS -->

                @for($i = 0; $i < $blocks->count(); $i += 2)

                    @php

                        $left = $blocks[$i] ?? null;

                        $right = $blocks[$i+1] ?? null;

                        $reverse = floor($i / 2) % 2;

                    @endphp

                    @if($left && !$right)

                        <div class="story-single reveal-item">

                            @if($left->type === 'text')

                                <div class="story-text-card depth-card">

                                    {!! nl2br(e($left->content)) !!}

                                </div>

                            @else

                                <div class="story-image-card depth-card">

                                    <img
                                        src="{{ $left->image }}"
                                        alt="Image">

                                </div>

                            @endif

                        </div>

                    @elseif($left && $right)

                        <div class="story-row {{ $reverse ? 'reverse' : '' }}">

                            <!-- LEFT -->

                            <div class="story-column reveal-item">

                                @if($left->type === 'text')

                                    <div class="story-text-card depth-card">

                                        {!! nl2br(e($left->content)) !!}

                                    </div>

                                @else

                                    <div class="story-image-card depth-card">

                                        <img
                                            src="{{ $left->image }}"
                                            alt="Image">

                                    </div>

                                @endif

                            </div>

                            <!-- RIGHT -->

                            <div class="story-column reveal-item">

                                @if($right->type === 'text')

                                    <div class="story-text-card depth-card">

                                        {!! nl2br(e($right->content)) !!}

                                    </div>

                                @else

                                    <div class="story-image-card depth-card">

                                        <img
                                            src="{{ $right->image }}"
                                            alt="Image">

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endif

                @endfor

            </section>

        @endif

        {{-- ========================================= --}}
        {{-- COMIC MODE --}}
        {{-- ========================================= --}}

        @if(strtolower($article->category) === 'comic')

            <section class="comic-3d-wrapper">

                @if($article->description)

                    <div class="comic-description-card depth-card">

                        {!! nl2br(e($article->description)) !!}

                    </div>

                @endif

                <div class="comic-grid">

                    @foreach(
                        $article->comicImages
                            ->sortBy('sort_order')
                        as $comic
                    )

                        <div class="comic-panel-card reveal-item depth-card">

                            <img
                                src="{{ $comic->image }}"
                                alt="Comic">

                        </div>

                    @endforeach

                </div>

            </section>

        @endif

        <!-- FOOTER -->

        <div class="article-footer-action">

            <a
                href="{{ route('articles') }}"
                class="article-back-btn">

                <i class="fas fa-arrow-left"></i>

                Back To Articles

            </a>

        </div>

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    /*
    ===============================
    REVEAL
    ===============================
    */

    const reveals =
        document.querySelectorAll('.reveal-item');

    const observer =
        new IntersectionObserver(entries => {

            entries.forEach(entry => {

                if(entry.isIntersecting){

                    entry.target.classList.add('active');

                }

            });

        },{
            threshold:.15
        });

    reveals.forEach(item => {

        observer.observe(item);

    });

    /*
    ===============================
    3D CARD
    ===============================
    */

    const cards =
        document.querySelectorAll('.depth-card');

    cards.forEach(card => {

        card.addEventListener('mousemove', e => {

            const rect =
                card.getBoundingClientRect();

            const x =
                e.clientX - rect.left;

            const y =
                e.clientY - rect.top;

            const centerX =
                rect.width / 2;

            const centerY =
                rect.height / 2;

            const rotateX =
                (y - centerY) / 30;

            const rotateY =
                (centerX - x) / 30;

            card.style.transform = `
                perspective(1400px)
                rotateX(${rotateX}deg)
                rotateY(${rotateY}deg)
                translateY(-10px)
            `;

        });

        card.addEventListener('mouseleave', () => {

            card.style.transform = `
                perspective(1400px)
                rotateX(0deg)
                rotateY(0deg)
                translateY(0px)
            `;

        });

    });

});

</script>

</x-app-layout>