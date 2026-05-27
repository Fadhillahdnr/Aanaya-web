<x-app-layout>

    <div class="dream-dashboard">

        <!-- =========================================
            HERO
        ========================================== -->
        <section class="dream-hero">

            <!-- BACKGROUND -->
            <div class="hero-slider">

                <div class="hero-slide active">
                    <img src="{{ asset('assets/bg/bg1.png') }}">
                </div>

                <div class="hero-slide">
                    <img src="{{ asset('assets/bg/bg2.jpeg') }}">
                </div>

                <div class="hero-slide">
                    <img src="{{ asset('assets/bg/bg3.png') }}">
                </div>

                <div class="hero-slide">
                    <img src="{{ asset('assets/bg/bg4.png') }}">
                </div>

            </div>

            <!-- OVERLAY -->
            <div class="hero-overlay"></div>

            <!-- GLOW -->
            <div class="hero-glow glow-1"></div>
            <div class="hero-glow glow-2"></div>

            <!-- CONTENT -->
            <div class="hero-container">

                <div class="hero-left">

                    <div class="dream-badge">
                        <span></span>
                        Aanaya UNIVERSE
                    </div>

                    <h1 class="dream-title">

                        @auth
                            Hello,
                            <span>{{ Auth::user()->name }}</span>
                        @endauth

                        @guest
                            Welcome To
                            <span>Aanaya</span>
                        @endguest

                    </h1>

                    <p class="dream-description">
                        Enter a dreamy cinematic universe filled with emotional music,
                        elegant visuals, and soft immersive experiences.
                    </p>

                    <div class="dream-buttons">

                        <a href="/music" class="dream-btn primary-btn">

                            <i class="fas fa-play"></i>

                            Listen Music

                        </a>

                        <a href="/gallery" class="dream-btn secondary-btn">

                            <i class="fas fa-image"></i>

                            Explore Gallery

                        </a>

                    </div>

                </div>

                <!-- FLOAT CARD -->
                <div class="hero-right">

                    @if(isset($latestMusic) && $latestMusic)

                        <div class="hero-music-card">

                            <div class="music-card-image">

                                <img
                                    src="{{ asset($latestMusic->cover_image) }}"
                                    alt="{{ $latestMusic->title }}">

                                <div class="music-image-overlay"></div>

                            </div>

                            <div class="music-card-content">

                                <div class="music-top">

                                    <span>Latest Release</span>

                                    <div class="music-wave">
                                        <i></i>
                                        <i></i>
                                        <i></i>
                                    </div>

                                </div>

                                <h3>
                                    {{ $latestMusic->title }}
                                </h3>

                                <p>
                                    {{ $latestMusic->artist }}
                                </p>

                                <div class="aanaya-player">

                                    <button class="aanaya-play-btn">
                                        <i class="fas fa-play"></i>
                                    </button>

                                    <div class="aanaya-audio-info">

                                        <span>
                                            {{ $latestMusic->title }}
                                        </span>

                                        <div class="aanaya-audio-wave">
                                            <i></i>
                                            <i></i>
                                            <i></i>
                                            <i></i>
                                        </div>

                                    </div>

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

                                    <audio class="custom-audio">

                                        <source
                                            src="{{ asset($latestMusic->audio_file) }}"
                                            type="audio/mpeg">

                                    </audio>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

            <!-- SLIDER NAV -->
            <div class="hero-navigation">

                <button class="hero-nav-btn" id="prevSlide">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button class="hero-nav-btn" id="nextSlide">
                    <i class="fas fa-chevron-right"></i>
                </button>

            </div>

        </section>

        <!-- =========================================
            DISCOVER
        ========================================== -->
        <section class="dream-section">

            <div class="section-heading">

                <div>
                    <span class="section-mini-title">
                        DISCOVER
                    </span>

                    <h2>
                        Explore The Universe
                    </h2>
                </div>

            </div>

            <div class="dream-grid">

                <a href="/music" class="dream-card">

                    <div class="dream-icon pink">
                        <i class="fas fa-music"></i>
                    </div>

                    <div class="dream-card-content">

                        <h3>Music</h3>

                        <p>
                            Emotional cinematic soundtracks and dreamy ambience.
                        </p>

                    </div>

                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>

                </a>

                <a href="/articles" class="dream-card">

                    <div class="dream-icon purple">
                        <i class="fas fa-book-open"></i>
                    </div>

                    <div class="dream-card-content">

                        <h3>Articles</h3>

                        <p>
                            Universe stories, thoughts, and exclusive updates.
                        </p>

                    </div>

                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>

                </a>

                <a href="/gallery" class="dream-card">

                    <div class="dream-icon blue">
                        <i class="fas fa-camera"></i>
                    </div>

                    <div class="dream-card-content">

                        <h3>Gallery</h3>

                        <p>
                            Soft visual collections with dreamy aesthetics.
                        </p>

                    </div>

                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>

                </a>

                <a href="/products" class="dream-card">

                    <div class="dream-icon peach">
                        <i class="fas fa-bag-shopping"></i>
                    </div>

                    <div class="dream-card-content">

                        <h3>Store</h3>

                        <p>
                            Explore premium official Aanaya merchandise.
                        </p>

                    </div>

                    <div class="card-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>

                </a>

            </div>

        </section>

        <!-- LIBRARY -->
        <section class="music-library"
                id="musicCollection">

            <!-- TOP -->
            <div class="library-top"
                data-aos="fade-up">

                <div>

                    <span class="section-subtitle">
                        LATEST RELEASES
                    </span>

                    <h2>
                        Music Collection
                    </h2>

                </div>

            </div>

            <!-- GRID -->
            <div class="music-grid">

                <!-- CARD -->
                <div class="spotify-card"
                    data-aos="fade-up"
                    data-aos-delay="100">

                    <div class="spotify-card-glow"></div>

                    <div class="card-top">

                        <span class="music-type">
                            Latest Single
                        </span>

                    </div>

                    <div class="spotify-content">

                        <h3>
                            Unfold
                        </h3>

                        <p>
                            Emotional indie-pop single
                            with cinematic dreamy vibes
                            and soft ambient textures.
                        </p>

                    </div>

                    <div class="spotify-embed">

                        <iframe
                            src="https://open.spotify.com/embed/track/0fQnKYxYVQupxyL8PKif9a?utm_source=generator&theme=0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy">
                        </iframe>

                    </div>

                </div>

                <!-- CARD -->
                <div class="spotify-card"
                    data-aos="fade-up"
                    data-aos-delay="200">

                    <div class="spotify-card-glow"></div>

                    <div class="card-top">

                        <span class="music-type">
                            Dream Pop
                        </span>


                    </div>

                    <div class="spotify-content">

                        <h3>
                            MSYL
                        </h3>

                        <p>
                            Floating melodies,
                            emotional atmosphere,
                            and dreamy synth layers
                            inside Aanaya’s universe.
                        </p>

                    </div>

                    <div class="spotify-embed">

                        <iframe
                            src="https://open.spotify.com/embed/track/1Uk8q00F6gDdEqXKAk5Wbr?utm_source=generator&theme=0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy">
                        </iframe>

                    </div>

                </div>

            </div>

        </section>

        <!-- =========================================
            QUOTE
        ========================================== -->
        <section class="dream-quote-section">

            <div class="dream-quote-card">

                <div class="quote-icon">
                    <i class="fas fa-quote-left"></i>
                </div>

                <h2>
                    “Music begins where words end.”
                </h2>

                <span>
                    — Aanaya Universe
                </span>

            </div>

        </section>

    </div>

    <!-- =========================================
        SCRIPT
    ========================================== -->
    <script>

        const slides =
            document.querySelectorAll('.hero-slide');

        let currentSlide = 0;

        function showSlide(index){

            slides.forEach(slide => {
                slide.classList.remove('active');
            });

            slides[index].classList.add('active');
        }

        function nextSlide(){

            currentSlide++;

            if(currentSlide >= slides.length){
                currentSlide = 0;
            }

            showSlide(currentSlide);
        }

        function prevSlide(){

            currentSlide--;

            if(currentSlide < 0){
                currentSlide = slides.length - 1;
            }

            showSlide(currentSlide);
        }

        document
            .getElementById('nextSlide')
            .addEventListener('click', nextSlide);

        document
            .getElementById('prevSlide')
            .addEventListener('click', prevSlide);

        setInterval(nextSlide, 7000);

    </script>

    <script>

        const playBtn =
            document.querySelector('.aanaya-play-btn');

        const audio =
            document.querySelector('.custom-audio');

        const icon =
            playBtn.querySelector('i');

        const volumeSlider =
            document.querySelector('.volume-slider');

        playBtn.addEventListener('click', () => {

            if(audio.paused){

                audio.play();

                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');

            }else{

                audio.pause();

                icon.classList.remove('fa-pause');
                icon.classList.add('fa-play');

            }

        });

        volumeSlider.addEventListener('input', () => {

            audio.volume = volumeSlider.value;

        });

    </script>

</x-app-layout>