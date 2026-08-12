<x-app-layout>
    @php
        $msylMusic = $recentMusics->first(fn ($music) => str_contains(strtolower($music->title), 'msyl') || str_contains(strtolower($music->title), 'someone you love'));
        $musicTracks = $recentMusics->map(fn ($music) => [
            'id' => $music->id,
            'slug' => $music->slug,
            'title' => $music->title,
            'artist' => $music->artist,
            'audio' => $music->audio_file,
            'cover' => $music->cover_image,
            'spotify' => $music->spotify_link,
            'youtube' => $music->youtube_link,
        ])->values();
    @endphp

    <main class="music-experience" data-music-experience data-track-count="{{ $recentMusics->count() }}">
        <div class="music-reactive-glow" aria-hidden="true"></div>

        <section class="music-cinema music-cinema--hero" data-music-scene="unfoldHero" aria-labelledby="music-hero-title">
            <div class="music-cinema__stage">
                <video class="music-cinema__video" muted playsinline preload="metadata" aria-hidden="true"
                    poster="{{ asset('videos/music-experience/posters/scene-unfold-letter-opens.webp') }}"
                    data-video-src="{{ asset('videos/music-experience/scene-unfold-letter-opens.mp4') }}"></video>
                <div class="music-cinema__veil" aria-hidden="true"></div>
                <div class="music-cinema__copy music-cinema__copy--hero">
                    <span class="music-eyebrow">00 / The sound awakens</span>
                    <h1 id="music-hero-title"><span>Aanaya</span>The sound<br>between feelings.</h1>
                    <p>Music <i></i> Memory <i></i> Emotion</p>
                </div>
                <a class="music-scroll-cue" href="#latest-release">Scroll into the sound <span aria-hidden="true">↓</span></a>
            </div>
        </section>

        <div class="music-paper-wash" aria-hidden="true"></div>

        <section class="music-cinema music-cinema--portal" data-music-scene="unfoldPortal" aria-labelledby="portal-title">
            <div class="music-cinema__stage">
                <video class="music-cinema__video" muted playsinline preload="none" aria-hidden="true"
                    poster="{{ asset('videos/music-experience/posters/scene-unfold-into-sound.webp') }}"
                    data-video-src="{{ asset('videos/music-experience/scene-unfold-into-sound.mp4') }}"
                    data-mobile-src="{{ asset('videos/music-experience/scene-unfold-into-sound-mobile.mp4') }}"></video>
                <div class="music-cinema__veil" aria-hidden="true"></div>
                <div class="music-cinema__copy music-cinema__copy--portal">
                    <span class="music-eyebrow">01 / Into the sound</span>
                    <h2 id="portal-title">Every feeling<br><em>finds a form.</em></h2>
                </div>
            </div>
        </section>

        <section id="latest-release" class="music-featured" aria-labelledby="featured-title">
            <div class="music-section-index">02</div>
            @if ($latestMusic)
                <div class="music-featured__media" data-cover-tilt>
                    <x-media-image :src="$latestMusic->cover_image" :alt="'Cover artwork '.$latestMusic->title"
                        :width="1000" :height="1000" crop="fill" priority="true"
                        sizes="(max-width: 767px) 84vw, 46vw" />
                    <canvas class="music-featured__wave" data-music-visualizer aria-hidden="true"></canvas>
                    <span class="music-featured__disc" aria-hidden="true"></span>
                </div>
                <div class="music-featured__copy" data-music-reveal>
                    <span class="music-eyebrow">Latest release</span>
                    <h2 id="featured-title">{{ $latestMusic->title }}</h2>
                    <p class="music-featured__artist">{{ $latestMusic->artist }}</p>
                    @if ($latestMusic->description)
                        <p class="music-featured__description">{{ $latestMusic->description }}</p>
                    @endif
                    @if ($latestMusic->release_date)
                        <time datetime="{{ $latestMusic->release_date }}">{{ \Carbon\Carbon::parse($latestMusic->release_date)->format('d F Y') }}</time>
                    @endif
                    <div class="music-actions">
                        @if ($latestMusic->audio_file)
                            <button type="button" class="music-play-action" data-play-track="{{ $latestMusic->id }}">
                                <span aria-hidden="true">▶</span> Play on Aanaya
                            </button>
                        @endif
                        @if ($latestMusic->spotify_link)
                            <a href="{{ $latestMusic->spotify_link }}" target="_blank" rel="noopener noreferrer">Spotify ↗</a>
                        @endif
                        @if ($latestMusic->youtube_link)
                            <a href="{{ $latestMusic->youtube_link }}" target="_blank" rel="noopener noreferrer">YouTube ↗</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="music-empty" data-music-reveal>
                    <span class="music-eyebrow">Latest release</span>
                    <h2 id="featured-title">The next feeling is still being written.</h2>
                    <p>New Aanaya music will unfold here soon.</p>
                </div>
            @endif
        </section>

        <section class="music-discography music-discography--light" aria-labelledby="discography-one-title">
            <header class="music-discography__header" data-music-reveal>
                <span class="music-eyebrow">03 / Discography — Act I</span>
                <h2 id="discography-one-title">Songs become<br><em>places.</em></h2>
                <p>{{ $totalMusic }} {{ \Illuminate\Support\Str::plural('release', $totalMusic) }} inside the Aanaya universe.</p>
            </header>
            <div class="music-release-list">
                @forelse ($recentMusics->take((int) ceil(max($recentMusics->count(), 1) / 2)) as $music)
                    @include('user.partials.music-release', ['music' => $music, 'releaseNumber' => $loop->iteration])
                @empty
                    <p class="music-discography__empty">No releases yet. The first chapter is still taking shape.</p>
                @endforelse
            </div>
        </section>

        <section class="music-cinema music-cinema--msyl" data-music-scene="msyl" aria-labelledby="msyl-title">
            <div class="music-cinema__stage">
                <video class="music-cinema__video" muted playsinline preload="none" aria-hidden="true"
                    poster="{{ asset('videos/music-experience/posters/scene-msyl-collage.webp') }}"
                    data-video-src="{{ asset('videos/music-experience/scene-msyl-collage.mp4') }}"
                    data-mobile-src="{{ asset('videos/music-experience/scene-msyl-collage-mobile.mp4') }}"></video>
                <div class="music-cinema__veil" aria-hidden="true"></div>
                <div class="music-cinema__copy music-cinema__copy--msyl">
                    <span class="music-eyebrow">04 / Collage of two hearts</span>
                    <h2 id="msyl-title">{{ $msylMusic?->title ?? 'MSYL' }}</h2>
                    <p>{{ $msylMusic?->artist ?? 'A story inside the Aanaya universe.' }}</p>
                    @if ($msylMusic?->audio_file)
                        <button type="button" class="music-play-action music-play-action--light" data-play-track="{{ $msylMusic->id }}">
                            <span aria-hidden="true">▶</span> Play this feeling
                        </button>
                    @endif
                </div>
            </div>
        </section>

        <section class="music-discography music-discography--dark" aria-labelledby="discography-two-title">
            <header class="music-discography__header" data-music-reveal>
                <span class="music-eyebrow">05 / The music universe</span>
                <h2 id="discography-two-title">Keep unfolding.</h2>
                <p>Every release leaves another memory behind.</p>
            </header>
            <div class="music-release-list">
                @foreach ($recentMusics->slice((int) ceil(max($recentMusics->count(), 1) / 2)) as $music)
                    @include('user.partials.music-release', ['music' => $music, 'releaseNumber' => $loop->iteration + (int) ceil($recentMusics->count() / 2)])
                @endforeach
            </div>
        </section>

        <section class="music-cinema music-cinema--ending" data-music-scene="ending" aria-labelledby="ending-title">
            <div class="music-cinema__stage">
                <video class="music-cinema__video" muted playsinline preload="none" aria-hidden="true"
                    poster="{{ asset('videos/music-experience/posters/scene-last-note.webp') }}"
                    data-video-src="{{ asset('videos/music-experience/scene-last-note.mp4') }}"></video>
                <img class="music-ending-poster" src="{{ asset('videos/music-experience/posters/ending-hold.webp') }}" alt="" aria-hidden="true">
                <div class="music-cinema__veil" aria-hidden="true"></div>
                <div class="music-cinema__copy music-cinema__copy--ending">
                    <span class="music-eyebrow">06 / The last note</span>
                    <h2 id="ending-title">Don't just<br>listen.<br><em>Feel it.</em></h2>
                </div>
            </div>
        </section>

        <section class="music-quiet-ending" aria-label="End of the Aanaya music experience">
            <span>07</span><p>The song continues with you.</p>
        </section>

        <script type="application/json" data-music-tracks>@json($musicTracks)</script>

        <aside class="music-player" data-music-player hidden aria-label="Aanaya music player">
            <audio data-music-player-audio preload="metadata"></audio>
            <img data-player-cover src="" alt="">
            <div class="music-player__identity">
                <strong data-player-title>Choose a release</strong>
                <span data-player-artist>Aanaya</span>
            </div>
            <button type="button" class="music-player__transport" data-player-previous aria-label="Previous track">‹</button>
            <button type="button" class="music-player__transport music-player__transport--primary" data-player-toggle aria-label="Play"><span aria-hidden="true">▶</span></button>
            <button type="button" class="music-player__transport" data-player-next aria-label="Next track">›</button>
            <div class="music-player__timeline">
                <span data-player-current>0:00</span>
                <input type="range" data-player-seek min="0" max="0" value="0" step="0.01" aria-label="Seek through track">
                <span data-player-duration>0:00</span>
            </div>
            <label class="music-player__volume">
                <span class="sr-only">Volume</span><span aria-hidden="true">◖</span>
                <input type="range" data-player-volume min="0" max="1" value="0.8" step="0.01" aria-label="Volume">
            </label>
            <button type="button" class="music-player__expand" data-player-expand aria-expanded="false" aria-label="Show player details">⌃</button>
            <div class="music-player__status" data-player-status role="status" aria-live="polite"></div>
        </aside>

        <noscript><style>.music-cinema{height:auto!important}.music-cinema__stage{position:relative!important;min-height:82svh}.music-player{display:none!important}</style></noscript>
    </main>
</x-app-layout>
