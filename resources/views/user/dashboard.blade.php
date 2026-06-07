<x-app-layout>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <div class="dream-dashboard">

        <!-- =========================================================
            HERO SECTION
        ========================================================== -->
        <section class="dream-hero">

            <!-- PARALLAX BG -->
            <div class="hero-parallax"></div>

            <!-- BACKGROUND SLIDER -->
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

            <!-- PARTICLES -->
            <div class="floating-particles">

                <span></span>
                <span></span>
                <span></span>
                <span></span>
                <span></span>

            </div>

            <!-- CONTENT -->
            <div class="hero-container">

                <!-- LEFT -->
                <div class="hero-left reveal reveal-left">

                    <div class="dream-badge">

                        <span></span>

                        DREAMY CINEMATIC EXPERIENCE

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
                        immersive visuals, soft aesthetics, and emotional storytelling
                        that creates a unique digital experience.

                    </p>

                    <div class="dream-buttons">

                        <a href="/music"
                           class="dream-btn primary-btn magnetic-btn">

                            <i class="fas fa-play"></i>

                            Listen Music

                        </a>

                        <a href="/gallery"
                           class="dream-btn secondary-btn magnetic-btn">

                            <i class="fas fa-image"></i>

                            Explore Gallery

                        </a>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="hero-right reveal reveal-right">

                    @if(isset($latestVideos) && $latestVideos->count())

                        <div class="hero-video-card tilt-card">

                            <div class="hero-video-wrapper">

                                <video
                                    id="heroVideo"
                                    autoplay
                                    muted
                                    playsinline
                                    class="hero-video">

                                    <source
                                        src="{{ asset($latestVideos[0]->video_file) }}"
                                        type="video/mp4">

                                </video>

                            </div>

                        </div>

                    @endif

                </div>

            <!-- NAV -->
            <div class="hero-navigation">

                <button class="hero-nav-btn"
                        id="prevSlide">

                    <i class="fas fa-chevron-left"></i>

                </button>

                <button class="hero-nav-btn"
                        id="nextSlide">

                    <i class="fas fa-chevron-right"></i>

                </button>

            </div>

        </section>

        <!-- =========================================================
            CHARACTER WRAPPER
        ========================================================= -->

        <div class="character-section-wrapper">

            <div class="floating-character">

                <div class="floating-character-glow"></div>

                <video
                    autoplay
                    muted
                    loop
                    playsinline
                    class="floating-character-video">

                    <source
                        src="{{ asset('assets/character/aanaya.webm') }}"
                        type="video/webm">

                    <source
                        src="{{ asset('assets/character/aanaya.mp4') }}"
                        type="video/mp4">

                </video>

            </div>

        </div>
        

        <!-- =========================================================
            AANAYA SIGNATURE VIDEO
        ========================================================= -->
        <section class="aanaya-signature-section">

            <div class="aanaya-signature-container">

                <!-- VIDEO -->
                <div class="aanaya-signature-video">

                    <video
                        autoplay
                        muted
                        loop
                        playsinline>

                        <source
                            src="{{ asset('assets/video/logo.mp4') }}"
                            type="video/mp4">

                    </video>

                </div>

                <!-- CONTENT -->
                <div class="aanaya-signature-content">

                    <span class="aanaya-signature-badge">

                        AANAYA VISUAL IDENTITY

                    </span>

                    <h2>

                        A Dream
                        <span>
                            In Motion
                        </span>

                    </h2>

                    <p>

                        Every frame carries the essence of Aanaya —
                        dreamy visuals, emotional storytelling,
                        and cinematic atmospheres blended into
                        a single experience.

                    </p>

                    <a href="/gallery"
                    class="aanaya-signature-btn">

                        <i class="fas fa-images"></i>

                        Explore Gallery

                    </a>

                </div>

            </div>

        </section>
        <!-- =========================================================
            DISCOVER
        ========================================================= -->
        <section class="dream-section reveal-section discover-section">

            <div class="section-heading reveal reveal-up">

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

                <!-- MUSIC -->
                <a href="/music"
                class="dream-card delay-1">

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

                <!-- ARTICLES -->
                <a href="/articles"
                class="dream-card delay-2">

                    <div class="dream-icon purple">

                        <i class="fas fa-book-open"></i>

                    </div>

                    <div class="dream-card-content">

                        <h3>Articles</h3>

                        <p>
                            Stories, thoughts, and emotional universe updates.
                        </p>

                    </div>

                    <div class="card-arrow">

                        <i class="fas fa-arrow-right"></i>

                    </div>

                </a>

                <!-- GALLERY -->
                <a href="/gallery"
                class="dream-card delay-3">

                    <div class="dream-icon blue">

                        <i class="fas fa-camera"></i>

                    </div>

                    <div class="dream-card-content">

                        <h3>Gallery</h3>

                        <p>
                            Elegant visual collections with dreamy aesthetics.
                        </p>

                    </div>

                    <div class="card-arrow">

                        <i class="fas fa-arrow-right"></i>

                    </div>

                </a>

                <!-- STORE -->
                <a href="/merchandise"
                class="dream-card delay-4">

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

        <!-- =========================================================
            MEMBERS SECTION
        ========================================================= -->
        <section class="members-section reveal-section">

            <div class="section-heading reveal reveal-up">

                <div>

                    <span class="section-mini-title">
                        OUR MEMBERS
                    </span>

                    <h2>
                        Meet The Souls Behind Aanaya
                    </h2>

                    <p>
                        Every melody begins with a heartbeat.
                        Meet the dreamers who bring Aanaya's
                        cinematic universe to life.
                    </p>

                </div>

            </div>

            <!-- FEATURED MEMBER -->
            <div class="featured-member reveal reveal-up">

                <div class="featured-member-image">

                    <img
                        src="{{ asset('assets/members/member1.jpg') }}"
                        alt="Aanaya">

                </div>

                <div class="featured-member-content">

                    <span class="member-role">

                        Lead Vocalist • Songwriter

                    </span>

                    <h3>
                        Aanaya
                    </h3>

                    <p>

                        The emotional voice behind Aanaya.
                        Through intimate lyrics and dreamy
                        melodies, she transforms feelings
                        into cinematic stories.

                    </p>

                    <div class="member-tags">

                        <span>Dream Pop</span>

                        <span>Lyrics</span>

                        <span>Vocals</span>

                    </div>

                </div>

            </div>

            <!-- MEMBER GRID -->
            <div class="members-grid">

                <!-- MEMBER -->
                <div class="member-card reveal reveal-up">

                    <div class="member-image">

                        <img
                            src="{{ asset('assets/members/member2.png') }}"
                            alt="Falisha">

                    </div>

                    <div class="member-content">

                        <span class="member-role">

                            Singer • Visual Artist

                        </span>

                        <h4>
                            Falisha
                        </h4>

                        <p>
                            Creates dreamy visuals
                            and aesthetic concepts.
                        </p>

                    </div>

                </div>

                <!-- MEMBER -->
                <div class="member-card reveal reveal-up">

                    <div class="member-image">

                        <img
                            src="{{ asset('assets/members/member3.png') }}"
                            alt="Ren">

                    </div>

                    <div class="member-content">

                        <span class="member-role">

                            Producer

                        </span>

                        <h4>
                            Keanu
                        </h4>

                        <p>
                            Architect of cinematic
                            soundscapes and ambience.
                        </p>

                    </div>

                </div>

                <!-- MEMBER -->
                <div class="member-card reveal reveal-up">

                    <div class="member-image">

                        <img
                            src="{{ asset('assets/members/member4.png') }}"
                            alt="Mika">

                    </div>

                    <div class="member-content">

                        <span class="member-role">

                            Composer

                        </span>

                        <h4>
                            Rangga
                        </h4>

                        <p>
                            Turns emotions into
                            unforgettable melodies.
                        </p>

                    </div>

                </div>

            </div>

        </section>

        <!-- =========================================================
            MUSIC SECTION
        ========================================================= -->
        <section class="dashboard-dream-section-music reveal-section">

            <!-- HEADING -->
            <div class="dashboard-section-heading-music reveal reveal-up">

                <div>

                    <span class="dashboard-section-mini-title-music">
                        MUSIC COLLECTION
                    </span>

                    <h2>
                        Latest Dream Releases
                    </h2>

                </div>

            </div>

            <a href="/music"
                class="dream-btn dream-btn-msc primary-btn magnetic-btn">
                    <i class="fas fa-play"></i>
                    Listen Music
            </a>

            <!-- GRID -->
            <div class="dashboard-music-showcase-grid">

                <!-- CARD -->
                <div class="dashboard-spotify-card tilt-card reveal reveal-left">

                    <div class="dashboard-spotify-card-glow"></div>

                    <!-- TOP -->
                    <div class="dashboard-card-top">

                        <span class="dashboard-music-type">
                            Latest Single
                        </span>

                    </div>

                    <!-- CONTENT -->
                    <div class="dashboard-spotify-content">

                        <h3>
                            Unfold
                        </h3>

                        <p>
                            Emotional indie-pop single with cinematic dreamy vibes.
                        </p>

                    </div>

                    <!-- EMBED -->
                    <div class="dashboard-spotify-embed">

                        <iframe
                            src="https://open.spotify.com/embed/track/0fQnKYxYVQupxyL8PKif9a?utm_source=generator&theme=0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy">
                        </iframe>

                    </div>

                </div>

                <!-- CARD -->
                <div class="dashboard-spotify-card tilt-card reveal reveal-right">

                    <div class="dashboard-spotify-card-glow"></div>

                    <!-- TOP -->
                    <div class="dashboard-card-top">

                        <span class="dashboard-music-type">
                            Dream Pop
                        </span>

                    </div>

                    <!-- CONTENT -->
                    <div class="dashboard-spotify-content">

                        <h3>
                            MSYL
                        </h3>

                        <p>
                            Floating melodies and dreamy synth layers.
                        </p>

                    </div>

                    <!-- EMBED -->
                    <div class="dashboard-spotify-embed">

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

        <!-- =========================================================
            VISUAL UNIVERSE
        ========================================================= -->
        <section class="visual-universe-section reveal-section">

            <div class="section-heading reveal reveal-up">

                <div>

                    <span class="section-mini-title">
                        VISUAL UNIVERSE
                    </span>

                    <h2>
                        The World Of Aanaya
                    </h2>

                    <p>
                        Every song has a visual soul.
                        Explore the cinematic aesthetics,
                        dreamy colors, and emotional atmosphere
                        that shape the Aanaya universe.
                    </p>

                </div>

            </div>

            <div class="visual-universe-grid">

                <!-- BIG -->
                <div class="visual-card visual-large">

                    <img
                        src="{{ asset('assets/visual/visual1.png') }}"
                        alt="Aanaya Visual">

                    <div class="visual-overlay">

                        <span>
                            Dream Pop Aesthetic
                        </span>

                        <h3>
                            Soft Colors & Endless Feelings
                        </h3>

                    </div>

                </div>

                <!-- SMALL -->
                <div class="visual-card">

                    <img
                        src="{{ asset('assets/visual/visual2.png') }}"
                        alt="Aanaya Visual">

                    <div class="visual-overlay">

                        <span>
                            Cinematic
                        </span>

                        <h3>
                            Midnight Memories
                        </h3>

                    </div>

                </div>

                <!-- SMALL -->
                <div class="visual-card">

                    <img
                        src="{{ asset('assets/visual/visual3.png') }}"
                        alt="Aanaya Visual">

                    <div class="visual-overlay">

                        <span>
                            Emotional
                        </span>

                        <h3>
                            Stories Between Notes
                        </h3>

                    </div>

                </div>

            </div>

        </section>

        {{-- visual vidio --}}

        <section class="aanaya-visual-section">

            <div class="aanaya-visual-video">

                <video
                    autoplay
                    muted
                    loop
                    playsinline>

                    <source
                        src="{{ asset('assets/visual/visualaanaya.mp4') }}"
                        type="video/mp4">

                </video>

                <div class="aanaya-visual-overlay"></div>

            </div>

            <div class="aanaya-visual-content">

                <span>
                    VISUAL IDENTITY
                </span>

                <h2>
                    Dreams Have Colors.
                    Music Has Shapes.
                </h2>

                <p>
                    Aanaya is more than music.
                    It is a visual experience built
                    from soft pink tones, cinematic
                    storytelling, floating emotions,
                    and dreamlike memories.
                </p>

            </div>

        </section>

        <!-- =========================================================
            QUOTE
        ========================================================== -->
        <section class="dream-quote-section reveal-section">

            <div class="dream-quote-card reveal reveal-zoom">

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

    <!-- =========================================================
        SCRIPT
    ========================================================== -->
    <script>

        /* =========================================================
            HERO SLIDER
        ========================================================== */

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

        /* =========================================================
            SCROLL REVEAL
        ========================================================== */

        const reveals =
            document.querySelectorAll('.reveal');

        function revealOnScroll(){

            reveals.forEach(el => {

                const windowHeight =
                    window.innerHeight;

                const revealTop =
                    el.getBoundingClientRect().top;

                const revealPoint = 120;

                if(revealTop < windowHeight - revealPoint){

                    el.classList.add('active');

                }

            });

        }

        window.addEventListener(
            'scroll',
            revealOnScroll
        );

        revealOnScroll();

        /* =========================================================
            TILT EFFECT
        ========================================================== */

        const tiltCards =
            document.querySelectorAll('.tilt-card');

        tiltCards.forEach(card => {

            card.addEventListener(
                'mousemove',
                (e) => {

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
                        ((y - centerY) / 25);

                    const rotateY =
                        ((centerX - x) / 25);

                    card.style.transform =
                        `
                        perspective(1000px)
                        rotateX(${rotateX}deg)
                        rotateY(${rotateY}deg)
                        translateY(-8px)
                        `;

                }
            );

            card.addEventListener(
                'mouseleave',
                () => {

                    card.style.transform =
                        `
                        perspective(1000px)
                        rotateX(0deg)
                        rotateY(0deg)
                        translateY(0px)
                        `;

                }
            );

        });

        /* =========================================================
            PARALLAX
        ========================================================== */

        window.addEventListener(
            'scroll',
            () => {

                const scrolled =
                    window.pageYOffset;

                const parallax =
                    document.querySelector('.hero-parallax');

                parallax.style.transform =
                    `translateY(${scrolled * 0.35}px)`;

            }
        );

    </script>


    <script>

        /*
        =========================================================
        HERO VIDEO AUTO PLAY DATABASE
        =========================================================
        */

        const heroVideo =
            document.getElementById('heroVideo');

        /*
        =========================================================
        VIDEO LIST
        =========================================================
        */

        const videoList = [

            @foreach($latestVideos as $video)

                "{{ asset($video->video_file) }}",

            @endforeach

        ];

        /*
        =========================================================
        DEBUG
        =========================================================
        */

        console.log(videoList);

        /*
        =========================================================
        CURRENT INDEX
        =========================================================
        */

        let currentVideo = 0;

        /*
        =========================================================
        FUNCTION NEXT VIDEO
        =========================================================
        */

        function nextVideo(){

            currentVideo++;

            /*
            BALIK KE VIDEO PERTAMA
            */
            if(currentVideo >= videoList.length){

                currentVideo = 0;

            }

            /*
            GANTI VIDEO
            */
            heroVideo.src =
                videoList[currentVideo];

            /*
            LOAD VIDEO BARU
            */
            heroVideo.load();

            /*
            PLAY VIDEO BARU
            */
            heroVideo.play();

        }

        /*
        =========================================================
        SAAT VIDEO SELESAI
        =========================================================
        */

        heroVideo.onended = function(){

            nextVideo();

        };

    </script>

    <script>

    document.addEventListener('DOMContentLoaded', () => {

        const cards = document.querySelectorAll('.dream-card');

        const observer = new IntersectionObserver((entries) => {

            entries.forEach(entry => {

                if(entry.isIntersecting){

                    entry.target.classList.add('active');

                }

            });

        }, {
            threshold: 0.15
        });

        cards.forEach(card => {
            observer.observe(card);
        });

    });

    </script>


</x-app-layout>