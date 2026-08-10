<x-app-layout>
    @php
        $latestVideo = $latestVideos->first();
        $heroVisual = $latestVideo?->thumbnail ?: asset('assets/bg/bg2.jpeg');
    @endphp

    <main class="dashboard-experience" data-dashboard-experience>
        <div class="dashboard-experience__grain" aria-hidden="true"></div>

        <div class="dashboard-chapter" data-dashboard-chapter-indicator aria-hidden="true">
            <span data-dashboard-chapter-number>01</span>
            <i><b data-dashboard-chapter-progress></b></i>
            <small data-dashboard-chapter-name>Introduction</small>
        </div>

        <header class="dashboard-hero" data-dashboard-hero data-dashboard-chapter="Introduction" data-dashboard-chapter-number="01">
            <div class="dashboard-hero__backdrop" data-dashboard-depth="background">
                <x-media-image
                    :src="$heroVisual"
                    :alt="$latestVideo ? 'Latest Aanaya visual: '.$latestVideo->title : ''"
                    :width="1600"
                    :height="1000"
                    crop="fill"
                    sizes="100vw"
                    priority />
            </div>
            <div class="dashboard-hero__wash" aria-hidden="true"></div>

            <p class="dashboard-hero__greeting" data-hero-intro>
                @auth
                    Hello, {{ Auth::user()->name }} · Welcome into the Aanaya universe
                @else
                    Welcome into the Aanaya universe
                @endauth
            </p>

            <div class="dashboard-hero__title" data-dashboard-depth="title">
                <span aria-hidden="true">AANAYA</span>
                <h1>Aanaya</h1>
            </div>

            <figure class="dashboard-hero__portrait" data-dashboard-depth="portrait">
                <img src="{{ asset('assets/members/member1.jpg') }}" alt="Aanaya, lead vocalist and songwriter" width="896" height="1195" fetchpriority="high" decoding="async">
            </figure>

            <div class="dashboard-hero__copy" data-hero-intro>
                <p>Dreams don’t stay still.</p>
                <span>Music, stories and images shaped by feelings that refuse to disappear.</span>
            </div>

            <a class="dashboard-hero__scroll" href="#manifesto" data-hero-intro>
                <span>Scroll to discover</span><i aria-hidden="true"></i>
            </a>
        </header>

        <section class="dashboard-manifesto" id="manifesto" data-dashboard-manifesto data-dashboard-chapter="Manifesto" data-dashboard-chapter-number="02" aria-labelledby="manifesto-title">
            <div class="dashboard-manifesto__inner">
                <p class="dashboard-kicker">01 · A living universe</p>
                <h2 id="manifesto-title">
                    <span data-manifesto-line="left">A dream</span>
                    <span data-manifesto-line="right">should</span>
                    <span data-manifesto-line="scale"><em>move.</em></span>
                </h2>
                <p class="dashboard-manifesto__support">Music. Stories. Images. Feelings.</p>
            </div>
        </section>

        <section class="dashboard-explore" data-dashboard-explore data-dashboard-chapter="Explore" data-dashboard-chapter-number="03" aria-labelledby="explore-title">
            <header class="dashboard-section-heading">
                <p class="dashboard-kicker">02 · Discover</p>
                <h2 id="explore-title">Choose your way<br>into the story.</h2>
            </header>

            <nav class="dashboard-explore__nav" aria-label="Explore Aanaya">
                @php
                    $exploreItems = [
                        ['route' => 'music', 'number' => '01', 'label' => 'Music', 'detail' => 'Listen to the feelings', 'theme' => 'music'],
                        ['route' => 'articles', 'number' => '02', 'label' => 'Stories', 'detail' => 'Read what stayed unspoken', 'theme' => 'stories'],
                        ['route' => 'gallery', 'number' => '03', 'label' => 'Visuals', 'detail' => 'See the universe unfold', 'theme' => 'visuals'],
                        ['route' => 'merchandise', 'number' => '04', 'label' => 'Store', 'detail' => 'Keep a piece of Aanaya', 'theme' => 'store'],
                    ];
                @endphp

                @foreach ($exploreItems as $item)
                    <a href="{{ route($item['route']) }}" class="dashboard-explore__item" data-explore-item data-dream-theme="{{ $item['theme'] }}">
                        <span>{{ $item['number'] }}</span>
                        <strong>{{ $item['label'] }}</strong>
                        <small>{{ $item['detail'] }}</small>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14m0 0-6-6m6 6-6 6"/></svg>
                    </a>
                @endforeach
            </nav>

            <div class="dashboard-explore__paper-trail" data-explore-paper-trail data-dream-theme="music" aria-hidden="true"></div>
        </section>

        <section class="dashboard-members" data-dashboard-members data-dashboard-chapter="The people" data-dashboard-chapter-number="04" aria-labelledby="members-title">
            <header class="dashboard-members__heading dashboard-section-heading">
                <p class="dashboard-kicker">03 · The people</p>
                <h2 id="members-title">Meet the souls<br><em>behind Aanaya.</em></h2>
                <p>Every melody begins with a heartbeat. Four people turn intimate feelings into one cinematic universe.</p>
            </header>

            <div class="dashboard-members__composition">
                <article class="dashboard-member dashboard-member--aanaya" data-member-depth="10">
                    <span class="dashboard-member__role-bg" aria-hidden="true">VOCALS</span>
                    <figure><img src="{{ asset('assets/members/member1.jpg') }}" alt="Aanaya" width="896" height="1195" loading="lazy" decoding="async"></figure>
                    <div><p>Lead Vocalist · Songwriter</p><h3>Aanaya</h3><span>The emotional voice transforming intimate lyrics into cinematic stories.</span></div>
                </article>

                <article class="dashboard-member dashboard-member--falisha" data-member-depth="18">
                    <span class="dashboard-member__role-bg" aria-hidden="true">VISUALS</span>
                    <figure><img src="{{ asset('assets/members/member2.webp') }}" alt="Falisha" width="1200" height="1598" loading="lazy" decoding="async"></figure>
                    <div><p>Singer · Visual Artist</p><h3>Falisha</h3><span>Creates the dreamy visual language and aesthetic concepts of Aanaya.</span></div>
                </article>

                <article class="dashboard-member dashboard-member--keanu" data-member-depth="13">
                    <span class="dashboard-member__role-bg" aria-hidden="true">PRODUCER</span>
                    <figure><img src="{{ asset('assets/members/member3.webp') }}" alt="Keanu" width="1200" height="1598" loading="lazy" decoding="async"></figure>
                    <div><p>Producer</p><h3>Keanu</h3><span>Architect of the cinematic soundscapes and quiet ambience.</span></div>
                </article>

                <article class="dashboard-member dashboard-member--rangga" data-member-depth="21">
                    <span class="dashboard-member__role-bg" aria-hidden="true">COMPOSER</span>
                    <figure><img src="{{ asset('assets/members/member4.webp') }}" alt="Rangga" width="1200" height="1598" loading="lazy" decoding="async"></figure>
                    <div><p>Composer</p><h3>Rangga</h3><span>Turns fragile emotions into melodies that stay after the song ends.</span></div>
                </article>
            </div>
        </section>

        <section class="dashboard-music" data-dashboard-music data-dashboard-chapter="Music" data-dashboard-chapter-number="05" aria-labelledby="music-title">
            <header class="dashboard-music__heading">
                <p class="dashboard-kicker">04 · Listen</p>
                <h2 id="music-title">Songs made<br>to be <em>felt.</em></h2>
                <a href="{{ route('music') }}">Explore all music <span aria-hidden="true">↗</span></a>
            </header>

            <div class="dashboard-releases">
                <article class="dashboard-release dashboard-release--unfold" data-release>
                    <div class="dashboard-release__visual"><img src="{{ asset('assets/visual/visual1.webp') }}" alt="Unfold release artwork" width="1080" height="1080" loading="lazy" decoding="async"></div>
                    <div class="dashboard-release__content">
                        <p>Latest single · Aanaya</p><h3>UNFOLD</h3>
                        <span>Emotional indie-pop wrapped in a quiet cinematic atmosphere.</span>
                        <button type="button" class="dashboard-listen" data-spotify-load data-spotify-src="https://open.spotify.com/embed/track/0fQnKYxYVQupxyL8PKif9a?utm_source=generator&theme=0" aria-expanded="false">Listen on Spotify</button>
                        <div class="dashboard-release__embed" data-spotify-mount></div>
                    </div>
                </article>

                <article class="dashboard-release dashboard-release--msyl" data-release>
                    <div class="dashboard-release__visual"><img src="{{ asset('assets/visual/visual3.webp') }}" alt="MSYL release artwork" width="1376" height="768" loading="lazy" decoding="async"></div>
                    <div class="dashboard-release__content">
                        <p>Dream pop · Aanaya</p><h3>MSYL</h3>
                        <span>Floating melodies, soft synth layers and memories that never quite leave.</span>
                        <button type="button" class="dashboard-listen" data-spotify-load data-spotify-src="https://open.spotify.com/embed/track/1Uk8q00F6gDdEqXKAk5Wbr?utm_source=generator&theme=0" aria-expanded="false">Listen on Spotify</button>
                        <div class="dashboard-release__embed" data-spotify-mount></div>
                    </div>
                </article>
            </div>
        </section>

        <section class="dashboard-visuals" data-dashboard-visuals data-dashboard-chapter="Visual universe" data-dashboard-chapter-number="06" aria-labelledby="visuals-title">
            <header class="dashboard-visuals__heading dashboard-section-heading">
                <p class="dashboard-kicker">05 · Visual universe</p>
                <h2 id="visuals-title">The world<br><em>of Aanaya.</em></h2>
                <p>Every song has a visual soul: dreamy colors, paper memories and midnight feelings.</p>
            </header>

            <div class="dashboard-visuals__composition">
                @foreach ([
                    ['visual1.webp', 'Dream Pop Aesthetic', 'Soft Colors & Endless Feelings', '01', 10, 1080, 1080],
                    ['visual2.webp', 'Cinematic', 'Midnight Memories', '02', 18, 1545, 1999],
                    ['visual3.webp', 'Emotional', 'Stories Between Notes', '03', 13, 1376, 768],
                    ['visual4.webp', 'Paper Dreams', 'Letters We Never Sent', '04', 22, 896, 1194],
                ] as [$image, $category, $title, $number, $depth, $width, $height])
                    <figure class="dashboard-visual dashboard-visual--{{ $number }}" data-visual-depth="{{ $depth }}">
                        <div><img src="{{ asset('assets/visual/'.$image) }}" alt="{{ $title }}" width="{{ $width }}" height="{{ $height }}" loading="lazy" decoding="async"></div>
                        <figcaption><span>{{ $number }} · {{ $category }}</span><strong>{{ $title }}</strong></figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        <section class="dashboard-signature" data-dashboard-signature data-dashboard-chapter="Identity" data-dashboard-chapter-number="07" aria-labelledby="signature-title">
            <img src="{{ asset('assets/bg/bg4.webp') }}" alt="" width="1600" height="894" loading="lazy" decoding="async">
            <div>
                <p class="dashboard-kicker">06 · Aanaya visual identity</p>
                <h2 id="signature-title">A dream<br><em>in motion.</em></h2>
                <p>Dreamy visuals and emotional storytelling, shaped into a single feeling.</p>
                <a href="{{ route('gallery') }}">Explore the visual archive <span aria-hidden="true">↗</span></a>
            </div>
        </section>

        <section class="dashboard-ending" data-dashboard-chapter="Finale" data-dashboard-chapter-number="08" aria-labelledby="ending-title">
            <p class="dashboard-kicker">07 · Stay a little longer</p>
            <h2 id="ending-title">KEEP<br><em>DREAMING.</em></h2>
            <p>Music. Stories. Visuals. Aanaya.</p>
            <a href="{{ route('music') }}">Explore Aanaya <span aria-hidden="true">→</span></a>
        </section>

        <div class="dashboard-cursor" data-dashboard-cursor aria-hidden="true"><span>Explore</span></div>
    </main>
</x-app-layout>
