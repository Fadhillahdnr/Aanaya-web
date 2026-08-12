<x-app-layout>
    <article class="about-experience" data-about-experience>
        <section class="about-orbit" data-about-orbit aria-labelledby="about-title">
            <div class="about-orbit__stage">
                <div class="about-orbit__atmosphere" aria-hidden="true"></div>

                <header class="about-orbit__copy">
                    <p class="about-kicker">An indie collective / an emotional universe</p>
                    <h1 id="about-title"><span>About</span><strong>Aanaya</strong></h1>
                    <p class="about-orbit__line">Music that feels<br>like <em>a dream.</em></p>
                    <div class="about-orbit__moods" aria-label="Aanaya is dreamy, cinematic, and emotional">
                        <span>Dreamy</span><span>Cinematic</span><span>Emotional</span>
                    </div>
                </header>

                <div class="about-orbit__world" aria-hidden="true">
                    <figure class="about-orbit__card about-orbit__card--one" data-orbit-object data-depth="0.35" data-travel="-18">
                        <img src="{{ asset('assets/visual/visual1.webp') }}" alt="" width="1080" height="1080" decoding="async">
                        <figcaption>Visual / 01</figcaption>
                    </figure>
                    <figure class="about-orbit__card about-orbit__card--two" data-orbit-object data-depth="0.6" data-travel="-36">
                        <img src="{{ asset('assets/visual/visual2.webp') }}" alt="" width="1545" height="1999" decoding="async">
                        <figcaption>Memory / 02</figcaption>
                    </figure>
                    <figure class="about-orbit__card about-orbit__card--three" data-orbit-object data-depth="0.8" data-travel="-52">
                        <img src="{{ asset('assets/visual/visual4.webp') }}" alt="" width="896" height="1194" decoding="async">
                        <figcaption>Story / 03</figcaption>
                    </figure>
                    <figure class="about-orbit__card about-orbit__card--four" data-orbit-object data-depth="1" data-travel="-72">
                        <img src="{{ asset('images/about-image.png') }}" alt="" width="848" height="1264" decoding="async">
                        <figcaption>Universe / 04</figcaption>
                    </figure>
                    <figure class="about-orbit__card about-orbit__card--five" data-orbit-object data-depth="0.5" data-travel="-30">
                        <img src="{{ asset('assets/visual/visual3.webp') }}" alt="" width="1376" height="768" decoding="async">
                        <figcaption>Feeling / 05</figcaption>
                    </figure>
                    <div class="about-orbit__fragment about-orbit__fragment--one" data-orbit-object data-depth="0.25" data-travel="-24">Nostalgia</div>
                    <div class="about-orbit__fragment about-orbit__fragment--two" data-orbit-object data-depth="0.72" data-travel="-58">Hope</div>
                    <div class="about-orbit__paper about-orbit__paper--one" data-orbit-object data-depth="0.45" data-travel="-40"></div>
                    <div class="about-orbit__paper about-orbit__paper--two" data-orbit-object data-depth=".9" data-travel="-68"></div>
                </div>

                <p class="about-orbit__scroll" aria-hidden="true">Scroll through the memories <span>↓</span></p>
            </div>
        </section>

        <section class="about-story" aria-labelledby="about-story-title">
            <div class="about-story__heading" data-about-reveal>
                <p class="about-chapter">01 / Who is Aanaya?</p>
                <h2 id="about-story-title">More than<br><em>just music.</em></h2>
            </div>
            <div class="about-story__body">
                <div class="about-story__entry" data-about-reveal>
                    <span>Sound</span>
                    <p>Aanaya is an indie collective defined by dreamy, cinematic, and deeply emotional soundscapes—an invitation into warmth, nostalgia, and profound sentiment.</p>
                </div>
                <div class="about-story__entry" data-about-reveal>
                    <span>Soul</span>
                    <p>Through harmonic resonance, ethereal visuals, and intimate storytelling, the boundary between sound and soul begins to blur, giving every unspoken feeling a voice.</p>
                </div>
                <div class="about-story__entry" data-about-reveal>
                    <span>Memory</span>
                    <p>Each track revisits the quietest corners of the heart. Themes of nostalgia, loss, and hope become emotional journeys that connect us all.</p>
                </div>
            </div>
        </section>

        <section class="about-feeling" data-about-feeling aria-labelledby="about-feeling-title">
            <p class="about-chapter">02 / The feeling</p>
            <h2 id="about-feeling-title" class="sr-only">The emotional world of Aanaya</h2>
            <div class="about-feeling__words" aria-hidden="true">
                <span data-feeling-word data-speed="-5">Nostalgia</span>
                <span data-feeling-word data-speed="7">Loss</span>
                <span data-feeling-word data-speed="-3">Hope</span>
                <span data-feeling-word data-speed="5">Warmth</span>
            </div>
            <p data-about-reveal>Feelings do not arrive in straight lines. They overlap, disappear, return—and sometimes become a song.</p>
        </section>

        <section class="about-constellation" aria-labelledby="about-constellation-title">
            <header data-about-reveal>
                <p class="about-chapter">03 / The universe</p>
                <h2 id="about-constellation-title">Sound becomes<br><em>a visual memory.</em></h2>
            </header>
            <div class="about-constellation__scene">
                <figure class="about-constellation__media about-constellation__media--one" data-constellation-media>
                    <img src="{{ asset('assets/visual/visual2.webp') }}" alt="Aanaya cinematic visual in a dreamy atmosphere" width="1545" height="1999" loading="lazy" decoding="async">
                    <figcaption>Dream / visual archive</figcaption>
                </figure>
                <figure class="about-constellation__media about-constellation__media--two" data-constellation-media>
                    <img src="{{ asset('assets/visual/visual3.webp') }}" alt="A visual fragment from the Aanaya universe" width="1376" height="768" loading="lazy" decoding="async">
                    <figcaption>Emotion / in motion</figcaption>
                </figure>
                <figure class="about-constellation__media about-constellation__media--three" data-constellation-media>
                    <img src="{{ asset('assets/visual/visual4.webp') }}" alt="Aanaya artwork shaped by stories and paper memories" width="896" height="1194" loading="lazy" decoding="async">
                    <figcaption>Story / held softly</figcaption>
                </figure>
                <p class="about-constellation__quote">A soft-pink aesthetic meets a modern indie identity, building a multisensory world designed to linger.</p>
            </div>
        </section>

        <section class="about-identity" data-about-identity aria-labelledby="about-identity-title">
            <div class="about-identity__type" aria-hidden="true">AANAYA</div>
            <div class="about-identity__character" data-about-character>
                <video muted loop playsinline preload="none" aria-label="The Aanaya character in motion" data-about-character-video>
                    <source data-src="{{ asset('assets/character/aanaya.webm') }}" type="video/webm">
                    <source data-src="{{ asset('assets/character/aanaya.mp4') }}" type="video/mp4">
                </video>
                <noscript><img src="{{ asset('images/about-image.png') }}" alt="Aanaya visual identity" width="848" height="1264"></noscript>
            </div>
            <div class="about-identity__copy" data-about-reveal>
                <p class="about-chapter">04 / Identity</p>
                <h2 id="about-identity-title">A dream<br><em>with a voice.</em></h2>
                <p>Music and visuals move as one—an intimate language for emotions that are difficult to say aloud.</p>
            </div>
        </section>

        <section class="about-manifesto" aria-labelledby="about-manifesto-title">
            <p class="about-chapter">05 / Manifesto</p>
            <h2 id="about-manifesto-title" data-about-reveal>Not just <span>heard.</span><br><em>Felt.</em></h2>
            <p data-about-reveal>Aanaya creates a place for emotion to remain, even after the last note fades.</p>
        </section>
    </article>
</x-app-layout>
