<x-app-layout>

<div class="user-gallery-page">

    <!-- BACKGROUND -->
    <div class="gallery-bg glow-1"></div>
    <div class="gallery-bg glow-2"></div>

    <!-- HERO -->
    <section class="gallery-hero">

        <span class="gallery-badge">
            ✨ DREAMY MEMORIES
        </span>

        <h1>
            Beautiful <span>Gallery</span>
        </h1>

        <p>
            A collection of beautiful moments, dreamy memories,
            and aesthetic visuals captured in one magical place.
        </p>

    </section>

    <!-- MASONRY GRID -->
    <section class="gallery-grid">

        @forelse($galleries as $gallery)

            <div class="gallery-item">

                <!-- IMAGE -->
                <div class="gallery-image-wrap">

                    <img
                        src="{{ asset('uploads/gallery/' . $gallery->image) }}"
                        alt="{{ $gallery->title }}"
                        class="gallery-image">

                    <!-- OVERLAY -->
                    <div class="gallery-overlay">

                        <div class="gallery-overlay-content">

                            <span class="gallery-mini-badge">
                                ✨ Gallery
                            </span>

                            <h2>
                                {{ $gallery->title ?? 'Dreamy Moment' }}
                            </h2>

                            <p>
                                {{ $gallery->description }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty-gallery">

                <i class="fas fa-image"></i>

                <h2>No Gallery Yet</h2>

                <p>
                    Beautiful moments will appear here soon ✨
                </p>

            </div>

        @endforelse

    </section>

</div>

</x-app-layout>