@section('article-blok')
{{-- ===================================================== --}}
{{-- ARTICLE --}}
{{-- ===================================================== --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">


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

            {{-- ========================================= --}}
            {{-- SINGLE BLOCK (FULL WIDTH) --}}
            {{-- ========================================= --}}
            @if($left && !$right)

                <div class="article-single-block">

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

            {{-- ========================================= --}}
            {{-- NORMAL ZIG ZAG --}}
            {{-- ========================================= --}}
            @elseif($left && $right)

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