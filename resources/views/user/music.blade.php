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
    <section class="dream-section">

        <!-- HEADING -->
        <div class="section-heading">

            <div>

                <span class="section-mini-title">
                    MUSIC COLLECTION
                </span>

                <h2>
                    Recent Releases
                </h2>

            </div>

            <!-- FILTER -->
            <div class="music-filter">

                <button class="filter-btn active">
                    All
                </button>

                <button class="filter-btn">
                    Singles
                </button>

                <button class="filter-btn">
                    Albums
                </button>

            </div>

        </div>

        <!-- GRID -->
        <div class="dream-music-grid">

            @forelse($recentMusics as $music)

                <!-- CARD -->
                <div class="dream-music-card"
                     data-aos="fade-up">

                    <!-- COVER -->
                    <div class="music-image">

                        <img
                            src="{{ asset($music->cover_image) }}"
                            alt="{{ $music->title }}">

                        <div class="music-image-overlay"></div>

                        <!-- FLOATING PLAY -->
                        <button class="floating-play-btn">

                            <i class="fas fa-play"></i>

                        </button>

                    </div>

                    <!-- CONTENT -->
                    <div class="music-content">

                        <!-- META -->
                        <div class="music-meta">

                            <div>

                                <span class="music-tag">
                                    Dream Pop
                                </span>

                                <h3>
                                    {{ $music->title }}
                                </h3>

                                <p>
                                    {{ $music->artist }}
                                </p>

                            </div>

                        </div>

                        <!-- CUSTOM PLAYER -->
                        <div class="aanaya-player">

                            <!-- PLAY -->
                            <button class="aanaya-play-btn">

                                <i class="fas fa-play"></i>

                            </button>

                            <!-- INFO -->
                            <div class="aanaya-audio-info">

                                <span>
                                    {{ $music->title }}
                                </span>

                                <div class="aanaya-audio-wave">

                                    <i></i>
                                    <i></i>
                                    <i></i>
                                    <i></i>

                                </div>

                            </div>

                            <!-- VOLUME -->
                            <div class="aanaya-volume">

                                <i class="fas fa-volume-up"></i>

                                <input
                                    type="range"
                                    class="volume-slider"
                                    min="0"
                                    max="1"
                                    step="0.01"
                                    value="1">

                            </div>

                            <!-- AUDIO -->
                            <audio class="custom-audio">

                                <source
                                    src="{{ asset($music->audio_file) }}"
                                    type="audio/mpeg">

                            </audio>

                        </div>

                    </div>

                </div>

            @empty

                <!-- EMPTY -->
                <div class="dream-empty-box">

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

</div>

<!-- =========================================
    AUDIO SCRIPT
========================================== -->
<script>

    document
        .querySelectorAll('.dream-music-card')
        .forEach(card => {

            const playBtn =
                card.querySelector('.aanaya-play-btn');

            const floatingBtn =
                card.querySelector('.floating-play-btn');

            const audio =
                card.querySelector('.custom-audio');

            const icon =
                playBtn.querySelector('i');

            const floatingIcon =
                floatingBtn.querySelector('i');

            const volumeSlider =
                card.querySelector('.volume-slider');

            // =====================================
            // TOGGLE AUDIO
            // =====================================

            function toggleAudio(){

                // pause other audio
                document
                    .querySelectorAll('.custom-audio')
                    .forEach(otherAudio => {

                        if(otherAudio !== audio){

                            otherAudio.pause();

                            const parentCard =
                                otherAudio.closest('.dream-music-card');

                            parentCard
                                .querySelector('.aanaya-play-btn i')
                                .className =
                                'fas fa-play';

                            parentCard
                                .querySelector('.floating-play-btn i')
                                .className =
                                'fas fa-play';

                        }

                    });

                // current audio
                if(audio.paused){

                    audio.play();

                    icon.classList.remove('fa-play');
                    icon.classList.add('fa-pause');

                    floatingIcon.classList.remove('fa-play');
                    floatingIcon.classList.add('fa-pause');

                    card.classList.add('playing');

                }else{

                    audio.pause();

                    icon.classList.remove('fa-pause');
                    icon.classList.add('fa-play');

                    floatingIcon.classList.remove('fa-pause');
                    floatingIcon.classList.add('fa-play');

                    card.classList.remove('playing');

                }

            }

            // =====================================
            // BUTTON EVENTS
            // =====================================

            playBtn.addEventListener(
                'click',
                toggleAudio
            );

            floatingBtn.addEventListener(
                'click',
                toggleAudio
            );

            // =====================================
            // VOLUME
            // =====================================

            volumeSlider.addEventListener('input', () => {

                audio.volume = volumeSlider.value;

            });

            // =====================================
            // AUDIO ENDED
            // =====================================

            audio.addEventListener('ended', () => {

                icon.classList.remove('fa-pause');
                icon.classList.add('fa-play');

                floatingIcon.classList.remove('fa-pause');
                floatingIcon.classList.add('fa-play');

                card.classList.remove('playing');

            });

        });

</script>

</x-app-layout>