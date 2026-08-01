<x-app-layout>

<div class="user-gallery-page">

    <!-- =========================================
        BACKGROUND
    ========================================== -->
    <div class="gallery-noise"></div>

    <div class="gallery-bg glow-1"></div>
    <div class="gallery-bg glow-2"></div>

    <!-- =========================================
        HERO
    ========================================== -->
    <section class="gallery-hero">

        <span class="gallery-badge">
            <span class="badge-dot"></span>
            AANAYA MEMORIES
        </span>

        <h1>
            Dreamy
            <span>Gallery</span>
        </h1>

        <p>
            A curated universe of cinematic visuals,
            emotional moments,
            dreamy memories,
            and aesthetic stories from Aanaya.
        </p>

    </section>

    <!-- =========================================
        MASONRY GRID
    ========================================== -->
    <section class="gallery-masonry">

        @forelse($galleries as $gallery)

            <article class="gallery-card">

                <!-- IMAGE -->
                <div class="gallery-image-box">

                    <x-media-image
                        :src="filter_var($gallery->image, FILTER_VALIDATE_URL) ? $gallery->image : asset('uploads/gallery/' . $gallery->image)"
                        :alt="$gallery->title ?? 'Aanaya gallery image'"
                        :width="720"
                        sizes="(max-width: 640px) 94vw, (max-width: 1100px) 48vw, 25vw"
                        class="gallery-image" />


                    <!-- OVERLAY -->
                    <div class="gallery-overlay">

                        <div class="gallery-content">

                            <div class="gallery-text">

                                <h2>
                                    {{ $gallery->title ?? 'Dreamy Moment' }}
                                </h2>

                                @if($gallery->description)

                                    <p>
                                        {{ $gallery->description }}
                                    </p>

                                @endif

                            </div>

                            <button
                                class="gallery-action-btn open-gallery-modal"
                                data-image="{{ \App\Support\MediaUrl::image(filter_var($gallery->image, FILTER_VALIDATE_URL) ? $gallery->image : asset('uploads/gallery/' . $gallery->image), 1600) }}"
                                data-title="{{ $gallery->title }}"
                                data-description="{{ $gallery->description }}">
                                
                                <i class="fas fa-expand"></i>

                            </button>

                        </div>

                    </div>

                </div>

            </article>

        @empty

            <!-- EMPTY -->
            <div class="empty-gallery">

                <div class="empty-icon">
                    <i class="fas fa-image"></i>
                </div>

                <h2>
                    No Gallery Yet
                </h2>

                <p>
                    Beautiful memories and dreamy visuals
                    will appear here soon ✨
                </p>

            </div>

        @endforelse

    </section>

    <div class="media-pagination">{{ $galleries->onEachSide(1)->links() }}</div>

    <!-- =========================================
        FULLSCREEN MODAL
    ========================================== -->
    <div class="gallery-modal" id="galleryModal">

        <!-- CLOSE -->
        <button class="gallery-modal-close" id="closeGalleryModal">
            <i class="fas fa-times"></i>
        </button>

        <!-- BACKDROP -->
        <div class="gallery-modal-backdrop"></div>

        <!-- CONTENT -->
        <div class="gallery-modal-content">

            <img
                src=""
                alt=""
                decoding="async"
                class="gallery-modal-image"
                id="galleryModalImage">

            <div class="gallery-modal-info">

                <h2 id="galleryModalTitle">
                    Dreamy Moment
                </h2>

                <p id="galleryModalDescription">
                    Beautiful memory captured inside Aanaya universe.
                </p>

            </div>

        </div>

    </div>

</div>

<!-- =========================================
    SCRIPT
========================================== -->
<script>

document.addEventListener("DOMContentLoaded", () => {

    const modal = document.getElementById("galleryModal");

    const modalImage = document.getElementById("galleryModalImage");

    const modalTitle = document.getElementById("galleryModalTitle");

    const modalDescription = document.getElementById("galleryModalDescription");

    const closeBtn = document.getElementById("closeGalleryModal");

    const openButtons = document.querySelectorAll(".open-gallery-modal");

    // OPEN MODAL
    openButtons.forEach(button => {

        button.addEventListener("click", () => {

            const image = button.dataset.image;

            const title = button.dataset.title;

            const description = button.dataset.description;

            modalImage.src = image;

            modalTitle.innerText = title || "Dreamy Moment";

            modalDescription.innerText =
                description || "Beautiful memory captured inside Aanaya universe.";

            modal.classList.add("active");

            document.body.style.overflow = "hidden";

        });

    });

    // CLOSE
    closeBtn.addEventListener("click", closeModal);

    modal.addEventListener("click", (e) => {

        if(
            e.target.classList.contains("gallery-modal") ||
            e.target.classList.contains("gallery-modal-backdrop")
        ){
            closeModal();
        }

    });

    // ESC
    document.addEventListener("keydown", (e) => {

        if(e.key === "Escape"){
            closeModal();
        }

    });

    function closeModal(){

        modal.classList.remove("active");

        document.body.style.overflow = "";

        modalImage.removeAttribute("src");

    }

});

</script>

</x-app-layout>
