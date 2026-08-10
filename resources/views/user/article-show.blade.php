<x-app-layout>
    @php
        $orderedBlocks = $article->blocks->sortBy('sort_order')->values();
        $introBlock = $orderedBlocks->firstWhere('type', 'text');
        $storyBlocks = $introBlock
            ? $orderedBlocks->reject(fn ($block) => $block->is($introBlock))->values()
            : $orderedBlocks;
        $chapters = $storyBlocks->chunk(2)->values();
        $interludeAfterChapter = max(1, (int) ceil($chapters->count() / 2));
        $publishedDate = $article->published_at ?? $article->created_at;
        $hasOptimizedArticleVideos = file_exists(public_path('videos/article-experience/optimized/scene-reading.mp4'));
        $hasMobileOptimizedArticleVideos = file_exists(public_path('videos/article-experience/optimized/mobile/scene-reading.mp4'));
    @endphp

    <article
        class="article-experience"
        data-article-experience
        data-optimized-videos="{{ $hasOptimizedArticleVideos ? 'true' : 'false' }}"
        data-mobile-optimized-videos="{{ $hasMobileOptimizedArticleVideos ? 'true' : 'false' }}"
        itemscope
        itemtype="https://schema.org/Article"
    >
        <meta itemprop="headline" content="{{ $article->title }}">
        <meta itemprop="image" content="{{ $article->thumbnail }}">

        <canvas class="article-experience__canvas" data-article-canvas aria-hidden="true"></canvas>
        <div class="article-experience__grain" aria-hidden="true"></div>

        <aside class="article-progress" data-article-progress aria-label="Reading progress">
            <span class="article-progress__label">Reading</span>
            <span class="article-progress__track" aria-hidden="true">
                <span class="article-progress__fill" data-progress-fill></span>
            </span>
            <output class="article-progress__value" data-progress-value>0%</output>
        </aside>

        <div class="article-cursor" data-article-cursor aria-hidden="true">
            <span data-cursor-label>Explore</span>
        </div>

        <button class="article-sound" type="button" data-article-sound aria-pressed="false" aria-label="Enable cinematic sound">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9v6h4l5 4V5L9 9H5Zm12.5 1.2a3 3 0 0 1 0 3.6M19.8 7a7 7 0 0 1 0 10"/></svg>
            <span>Sound</span><strong data-sound-state>Off</strong>
        </button>

        <header class="article-cinematic" data-video-scene data-scene="reading">
            <div class="article-cinematic__sticky">
                <div class="article-cinematic__media" data-cinematic-media>
                    <video
                        data-scrub-video
                        poster="{{ \App\Support\MediaUrl::image($article->thumbnail, 1600, 1000, 'fill') }}"
                        muted
                        playsinline
                        preload="metadata"
                        aria-hidden="true"
                    ></video>

                    <x-media-image
                        :src="$article->thumbnail"
                        :alt="$article->title"
                        :width="1600"
                        :height="1000"
                        crop="fill"
                        sizes="100vw"
                        priority
                        class="article-cinematic__poster" />

                    <div class="article-cinematic__veil" aria-hidden="true"></div>
                    <div class="article-scene-transition article-scene-transition--burgundy" data-scene-transition aria-hidden="true"></div>
                </div>

                <div class="article-cinematic__content">
                    <p class="article-cinematic__eyebrow" data-hero-reveal>
                        <span>Aanaya Stories</span>
                        <span aria-hidden="true">No. {{ str_pad((string) $article->id, 2, '0', STR_PAD_LEFT) }}</span>
                    </p>

                    <h1 itemprop="name" data-hero-title>{{ $article->title }}</h1>

                    <div class="article-cinematic__meta" data-hero-reveal>
                        <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                            By <span itemprop="name">{{ $article->author->name }}</span>
                        </span>
                        <time itemprop="datePublished" datetime="{{ $publishedDate->toAtomString() }}">
                            {{ $publishedDate->format('d M Y') }}
                        </time>
                        <span>{{ strtoupper($article->category) }}</span>
                    </div>

                    <div class="article-cinematic__actions" data-hero-reveal>
                        <a href="#article-story" class="article-action article-action--primary" data-cursor="Enter">
                            Enter the story
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14m0 0 6-6m-6 6-6-6"/></svg>
                        </a>
                        <a href="{{ route('articles') }}" class="article-action article-action--quiet" data-cursor="Back">
                            All stories
                        </a>
                    </div>
                </div>

                <div class="article-cinematic__scroll" aria-hidden="true">
                    <span>Scroll to enter</span>
                    <i></i>
                </div>
            </div>
        </header>

        <section class="article-approach" data-video-scene data-scene="approach-book" aria-label="Approaching the book">
            <div class="article-approach__sticky" style="--scene-poster: url('{{ \App\Support\MediaUrl::image($article->thumbnail, 1440, 900, 'fill') }}')">
                <video data-scrub-video muted playsinline preload="none" aria-hidden="true"></video>
                <div class="article-scene-transition article-scene-transition--burgundy" data-scene-transition="entry" aria-hidden="true"></div>
                <div class="article-approach__copy" data-scene-copy>
                    <span>Chapter 00</span><p>Come a little closer.</p>
                </div>
                <div class="article-scene-transition article-scene-transition--paper" data-scene-transition="exit" aria-hidden="true"></div>
            </div>
        </section>

        <section class="article-portal" data-video-scene data-scene="enter-book" aria-label="Enter the story">
            <div class="article-portal__sticky" style="--scene-poster: url('{{ \App\Support\MediaUrl::image($article->thumbnail, 1440, 900, 'fill') }}')">
                <video
                    data-scrub-video
                    poster="{{ \App\Support\MediaUrl::image($article->thumbnail, 1440, 900, 'fill') }}"
                    muted
                    playsinline
                    preload="none"
                    aria-hidden="true"
                ></video>
                <div class="article-portal__paper" data-portal-paper aria-hidden="true"></div>
                <div class="article-portal__copy" data-portal-copy>
                    <span>Chapter 00</span>
                    <p>Some stories are not read.</p>
                    <strong>They are entered.</strong>
                </div>
            </div>
        </section>

        <main class="article-story" id="article-story" data-article-story>
            <header class="article-story__opening" data-story-reveal>
                <p class="article-story__kicker">The beginning</p>
                <h2>Open the first page.</h2>

                @if ($introBlock)
                    <div class="article-story__lead" itemprop="articleBody">
                        {!! nl2br(e($introBlock->content)) !!}
                    </div>
                @elseif ($article->description)
                    <div class="article-story__lead" itemprop="articleBody">
                        {!! nl2br(e($article->description)) !!}
                    </div>
                @endif
            </header>

            @forelse ($chapters as $chapterIndex => $chapterBlocks)
                @php
                    $chapterNumber = $chapterIndex + 1;
                    $isReversed = $chapterNumber % 2 === 0;
                @endphp

                <section
                    class="article-chapter {{ $isReversed ? 'article-chapter--reverse' : '' }}"
                    data-article-chapter
                    aria-labelledby="chapter-title-{{ $chapterNumber }}"
                >
                    <div class="article-chapter__marker" aria-hidden="true">
                        <span>{{ str_pad((string) $chapterNumber, 2, '0', STR_PAD_LEFT) }}</span>
                        <i></i>
                    </div>

                    <div class="article-chapter__layout">
                        @foreach ($chapterBlocks as $blockIndex => $block)
                            @if ($block->type === 'text')
                                <div class="article-chapter__text" data-story-reveal>
                                    <p class="article-chapter__eyebrow">Chapter {{ str_pad((string) $chapterNumber, 2, '0', STR_PAD_LEFT) }}</p>
                                    @if ($blockIndex === 0)
                                        <h2 id="chapter-title-{{ $chapterNumber }}">A page from the story</h2>
                                    @endif
                                    <div class="article-chapter__prose" itemprop="articleBody">
                                        {!! nl2br(e($block->content)) !!}
                                    </div>
                                </div>
                            @else
                                <figure class="article-chapter__visual" data-story-image data-cursor="Dream">
                                    <div class="article-chapter__image-mask">
                                        <x-media-image
                                            :src="$block->image"
                                            :alt="'Illustration for '.$article->title.', chapter '.$chapterNumber"
                                            :width="1200"
                                            :height="900"
                                            crop="fill"
                                            sizes="(max-width: 767px) 92vw, (max-width: 1100px) 80vw, 48vw" />
                                        <span class="article-chapter__light" aria-hidden="true"></span>
                                    </div>
                                    <figcaption>Chapter {{ str_pad((string) $chapterNumber, 2, '0', STR_PAD_LEFT) }} · Aanaya archive</figcaption>
                                </figure>
                            @endif
                        @endforeach
                    </div>
                </section>

                @if ($chapterNumber === $interludeAfterChapter && $chapters->count() > 1)
                    <section class="article-interlude" data-video-scene data-scene="paper-plane" aria-label="Turn the page">
                        <div class="article-interlude__sticky" style="--scene-poster: url('{{ \App\Support\MediaUrl::image($article->thumbnail, 1440, 900, 'fill') }}')">
                            <video
                                data-scrub-video
                                poster="{{ \App\Support\MediaUrl::image($article->thumbnail, 1440, 900, 'fill') }}"
                                muted
                                playsinline
                                preload="none"
                                aria-hidden="true"
                            ></video>
                            <div class="article-interlude__veil" aria-hidden="true"></div>
                            <p data-interlude-copy><span>Turn the page</span>The feeling changes, softly.</p>
                        </div>
                    </section>
                @endif
            @empty
                <section class="article-story__empty" aria-label="Story unavailable">
                    <p>This story is waiting for its next page.</p>
                </section>
            @endforelse
        </main>

        <footer class="article-ending" data-video-scene data-scene="ending">
          <div class="article-ending__sticky" style="--scene-poster: url('{{ \App\Support\MediaUrl::image($article->thumbnail, 1600, 1000, 'fill') }}')">
            <div class="article-ending__media" aria-hidden="true">
                <video
                    data-scrub-video
                    poster="{{ \App\Support\MediaUrl::image($article->thumbnail, 1600, 1000, 'fill') }}"
                    muted
                    playsinline
                    preload="none"
                ></video>
                <div class="article-ending__veil"></div>
            </div>

            <div class="article-ending__content" data-ending-content>
                <p>End of story</p>
                <h2>You’ve reached<br><em>the final page.</em></h2>
                <span>Thank you for staying with this feeling.</span>
                <a href="{{ route('articles') }}" class="article-action article-action--primary" data-cursor="Explore">
                    Explore another story
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m0 0-6-6m6 6-6 6"/></svg>
                </a>
            </div>
          </div>
        </footer>

        <p class="article-experience__status" data-article-status role="status" aria-live="polite"></p>
    </article>
</x-app-layout>
