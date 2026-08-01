<x-app-layout>

<div class="music-page">

    <!-- =========================================
        BACKGROUND
    ========================================== -->
    <div class="music-noise"></div>

    <div class="music-bg glow-1"></div>
    <div class="music-bg glow-2"></div>
    <div class="music-bg glow-3"></div>

    <!-- =========================================
        HERO
    ========================================== -->
    <section class="music-hero">

        <div class="music-hero-content"
             data-aos="fade-up">

            <span class="music-badge">

                <span class="badge-dot"></span>

                Aanaya Dreamy Music

            </span>

            <h1>

                Dreamy Sounds

                <span>
                    & Emotional Stories
                </span>

            </h1>

            <p>
                Dive into Aanaya’s cinematic universe 
                emotional melodies,
                floating atmospheres,
                and soft dreamy experiences crafted
                through music.
            </p>

        </div>

    </section>

    <!-- =========================================
         MUSIC COLLECTION
    ========================================== -->
    <section class="dream-music-section">

        <!-- HEADING -->
        <div class="dream-music-section-heading">

            <div>

                <span class="dream-music-section-mini-title">
                    MUSIC COLLECTION
                </span>

                <h2>
                    Recent Releases
                </h2>

            </div>

            <!-- FILTER -->
            <div class="dream-music-filter">

                <button class="dream-music-filter-btn active">
                    All
                </button>

                <button class="dream-music-filter-btn">
                    Singles
                </button>

                <button class="dream-music-filter-btn">
                    Albums
                </button>

            </div>

        </div>

        <!-- GRID -->
        <div class="dream-music-grid">

            @forelse($recentMusics as $music)

            <div class="dream-music-card"
                data-aos="fade-up">

                <!-- COVER -->
                <div class="dream-music-image">

                    <x-media-image :src="$music->cover_image" :alt="$music->title"
                        :width="640" :height="640" crop="fill"
                        sizes="(max-width: 700px) 92vw, 33vw" />

                    <div class="dream-music-image-overlay"></div>

                    <button
                        class="dream-music-floating-play-btn">

                        <i class="fas fa-play"></i>

                    </button>

                </div>

                <!-- CONTENT -->
                <div class="dream-music-content">

                    <div class="dream-music-meta">

                        <div>

                            <span class="dream-music-tag">
                                Latest Release
                            </span>

                            <h3>
                                {{ $music->title }}
                            </h3>

                            <p>
                                {{ $music->artist }}
                            </p>

                            @if($music->release_date)

                            <div class="dream-music-release">

                                <i class="fas fa-calendar"></i>

                                {{ \Carbon\Carbon::parse($music->release_date)->format('d M Y') }}

                            </div>

                            @endif

                            <!-- STREAMING LINKS -->
                            <div class="dream-music-stream-links">

                                @if($music->spotify_link)

                                <a
                                    href="{{ $music->spotify_link }}"
                                    target="_blank"
                                    class="spotify-link">

                                    <i class="fab fa-spotify"></i>

                                    Spotify

                                </a>

                                @endif

                                @if($music->youtube_link)

                                <a
                                    href="{{ $music->youtube_link }}"
                                    target="_blank"
                                    class="youtube-link">

                                    <i class="fab fa-youtube"></i>

                                    YouTube

                                </a>

                                @endif

                            </div>

                        </div>

                    </div>

                    <!-- PLAYER -->
                    <div class="dream-music-player">

                        <button
                            class="dream-music-play-btn">

                            <i class="fas fa-play"></i>

                        </button>

                        <div class="dream-music-audio-info">

                            <span>
                                {{ $music->title }}
                            </span>

                            <div class="dream-music-audio-wave">

                                <i></i>
                                <i></i>
                                <i></i>
                                <i></i>
                                
                            </div>
                        </div>
                        <!-- PROGRESS -->
                        <div class="dream-music-progress-wrap">

                            <span class="current-time">
                                0:00
                            </span>

                            <input
                                type="range"
                                class="dream-music-progress dream-music-volume-slider"
                                value="0"
                                min="0"
                                max="100">

                            <span class="duration">
                                0:00
                            </span>

                        </div>
                        

                    </div>


                    <!-- AUDIO -->
                    <audio
                        class="dream-music-audio"
                        preload="none"
                        data-src="{{ $music->audio_file }}">

                        <source
                            data-src="{{ $music->audio_file }}"
                            type="audio/mpeg">

                    </audio>

                </div>

            </div>

            @empty

            <div class="dream-music-empty-box">

                <i class="fas fa-music"></i>

                <h3>

                    No Music Yet

                </h3>

                <p>

                    Upload your first dreamy soundtrack.

                </p>

            </div>

            @endforelse

        </div>

    </section>

<!-- =========================================
    AUDIO SCRIPT
========================================== -->
<script>

document
    .querySelectorAll('.dream-music-card')
    .forEach(card => {

        const playBtn =
            card.querySelector(
                '.dream-music-play-btn'
            );

        const floatingBtn =
            card.querySelector(
                '.dream-music-floating-play-btn'
            );

        const audio =
            card.querySelector(
                '.dream-music-audio'
            );

        const icon =
            playBtn.querySelector('i');

        const floatingIcon =
            floatingBtn.querySelector('i');

        const volumeSlider =
            card.querySelector(
                '.dream-music-volume-slider'
            );

        const progress =
            card.querySelector(
                '.dream-music-progress'
            );

        const currentTime =
            card.querySelector(
                '.current-time'
            );

        const duration =
            card.querySelector(
                '.duration'
            );

        if(volumeSlider){

            volumeSlider.addEventListener(
                'input',
                () => {

                    audio.volume =
                        volumeSlider.value;

                }
            );

        }

        if(progress){

            progress.addEventListener(
                'input',
                () => {

                    audio.currentTime =
                        (progress.value / 100) *
                        audio.duration;

                }
            );

        }
        /*
        ==========================================
        FORMAT TIME
        ==========================================
        */

        function formatTime(seconds)
        {
            if (isNaN(seconds))
            {
                return "0:00";
            }

            const mins =
                Math.floor(seconds / 60);

            const secs =
                Math.floor(seconds % 60);

            return (
                mins +
                ':' +
                String(secs)
                    .padStart(2, '0')
            );
        }

        /*
        ==========================================
        PLAY / PAUSE
        ==========================================
        */

        function toggleAudio()
        {
            document
                .querySelectorAll(
                    '.dream-music-audio'
                )
                .forEach(otherAudio => {

                    if(otherAudio !== audio)
                    {
                        otherAudio.pause();

                        const otherCard =
                            otherAudio.closest(
                                '.dream-music-card'
                            );

                        otherCard
                            .querySelector(
                                '.dream-music-play-btn i'
                            )
                            .className =
                            'fas fa-play';

                        otherCard
                            .querySelector(
                                '.dream-music-floating-play-btn i'
                            )
                            .className =
                            'fas fa-play';

                        otherCard
                            .classList
                            .remove(
                                'playing'
                            );
                    }

                });

            if(audio.paused)
            {
                if(audio.dataset.loaded !== 'true')
                {
                    const source = audio.querySelector('source[data-src]');
                    if(source) source.src = source.dataset.src;
                    audio.dataset.loaded = 'true';
                    audio.load();
                }

                audio.play();

                icon.classList.remove(
                    'fa-play'
                );

                icon.classList.add(
                    'fa-pause'
                );

                floatingIcon.classList.remove(
                    'fa-play'
                );

                floatingIcon.classList.add(
                    'fa-pause'
                );

                card.classList.add(
                    'playing'
                );
            }
            else
            {
                audio.pause();

                icon.classList.remove(
                    'fa-pause'
                );

                icon.classList.add(
                    'fa-play'
                );

                floatingIcon.classList.remove(
                    'fa-pause'
                );

                floatingIcon.classList.add(
                    'fa-play'
                );

                card.classList.remove(
                    'playing'
                );
            }
        }

        /*
        ==========================================
        BUTTON EVENTS
        ==========================================
        */

        playBtn.addEventListener(
            'click',
            toggleAudio
        );

        floatingBtn.addEventListener(
            'click',
            toggleAudio
        );

        /*
        ==========================================
        AUDIO LOADED
        ==========================================
        */

        audio.addEventListener(
            'loadedmetadata',
            () =>
        {
            duration.innerText =
                formatTime(
                    audio.duration
                );
        });

        /*
        ==========================================
        AUDIO PROGRESS
        ==========================================
        */

        audio.addEventListener(
            'timeupdate',
            () =>
        {
            if(audio.duration)
            {
                progress.value =
                    (
                        audio.currentTime /
                        audio.duration
                    ) * 100;
            }

            currentTime.innerText =
                formatTime(
                    audio.currentTime
                );
        });

        /*
        ==========================================
        SEEK AUDIO
        ==========================================
        */

        progress.addEventListener(
            'input',
            () =>
        {
            if(audio.duration)
            {
                audio.currentTime =
                    (
                        progress.value / 100
                    ) *
                    audio.duration;
            }
        });

        /*
        ==========================================
        VOLUME
        ==========================================
        */

        volumeSlider.addEventListener(
            'input',
            () =>
        {
            audio.volume =
                volumeSlider.value;
        });

        /*
        ==========================================
        AUDIO ENDED
        ==========================================
        */

        audio.addEventListener(
            'ended',
            () =>
        {
            icon.className =
                'fas fa-play';

            floatingIcon.className =
                'fas fa-play';

            progress.value = 0;

            currentTime.innerText =
                '0:00';

            card.classList.remove(
                'playing'
            );
        });

    });

</script>

</x-app-layout>
