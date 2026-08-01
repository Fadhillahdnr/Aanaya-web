<x-app-layout>

<div class="comic-read-page">

    <!-- ===================================================== -->
    <!-- BACKGROUND GLOW -->
    <!-- ===================================================== -->

    <div class="comic-bg glow-1"></div>
    <div class="comic-bg glow-2"></div>

    <!-- ===================================================== -->
    <!-- HERO -->
    <!-- ===================================================== -->

    <section class="comic-hero">

        <!-- THUMBNAIL -->
        <div class="comic-hero-image">

            @if($comic->thumbnail)

                <x-media-image :src="$comic->thumbnail" :alt="$comic->title"
                    :width="1280" :height="720" crop="fill" sizes="100vw" priority />

            @else

                <div class="comic-no-thumbnail">

                    <i class="fas fa-image"></i>

                    <span>No Thumbnail</span>

                </div>

            @endif

        </div>

        <!-- CONTENT -->
        <div class="comic-hero-content">

            <span class="comic-badge">

                <i class="fas fa-book-open"></i>

                VISUAL STORY

            </span>

            <h1>
                {{ $comic->title }}
            </h1>

            <!-- DESCRIPTION -->
            <p class="comic-description">

                {{ $comic->description ?? 'No description available for this comic.' }}

            </p>

            <!-- META -->
            <div class="comic-meta">

                <span>

                    <i class="fas fa-user"></i>

                    {{ $comic->author->name ?? 'Aanaya' }}

                </span>

                <span>

                    <i class="fas fa-calendar"></i>

                    {{ $comic->created_at->format('d M Y') }}

                </span>

                <span>

                    <i class="fas fa-images"></i>

                    {{ $comic->comicImages->count() }}
                    Panels

                </span>

            </div>

            <!-- ACTIONS -->
            <div class="comic-actions">

                <a href="{{ url()->previous() }}"
                   class="back-btn">

                    <i class="fas fa-arrow-left"></i>

                    Back

                </a>

                <a href="#comicReader"
                   class="start-reading-btn">

                    <i class="fas fa-book-reader"></i>

                    Start Reading

                </a>

            </div>

        </div>

    </section>

    <!-- ===================================================== -->
    <!-- COMIC READER -->
    <!-- ===================================================== -->

    <section
        class="comic-reader"
        id="comicReader">

        @forelse($comic->comicImages as $panel)

            <div class="comic-panel">

                <x-media-image :src="$panel->image"
                    alt="Comic Panel {{ $loop->iteration }}"
                    :width="1200" sizes="(max-width: 800px) 100vw, 900px" />

                <!-- PANEL NUMBER -->
                <div class="panel-number">

                    {{ $loop->iteration }}

                </div>

            </div>

        @empty

            <!-- EMPTY -->
            <div class="empty-comic">

                <i class="fas fa-images"></i>

                <h2>No Comic Panels</h2>

                <p>
                    This comic does not have any uploaded images yet.
                </p>

            </div>

        @endforelse

    </section>

</div>

</x-app-layout>
