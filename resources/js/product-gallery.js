document.querySelectorAll('[data-product-gallery]').forEach((gallery) => {
    const track = gallery.querySelector('[data-product-gallery-track]');
    const slides = [...gallery.querySelectorAll('[data-product-gallery-slide]')];
    const thumbnails = [...gallery.querySelectorAll('[data-product-gallery-thumbnail]')];
    const currentLabel = gallery.querySelector('[data-product-gallery-current]');
    const previous = gallery.querySelector('[data-product-gallery-previous]');
    const next = gallery.querySelector('[data-product-gallery-next]');
    const viewport = gallery.querySelector('[data-product-gallery-viewport]');
    const zoomHint = gallery.querySelector('.user-product-gallery-zoom-hint');
    const dialog = document.querySelector('[data-product-lightbox]');
    const lightboxImage = dialog?.querySelector('[data-product-lightbox-image]');
    const lightboxCounter = dialog?.querySelector('[data-product-lightbox-counter]');
    const imageSlideIndexes = slides
        .map((slide, position) => slide.dataset.mediaType === 'image' ? position : null)
        .filter((position) => position !== null);
    let index = 0;
    let touchStartX = null;
    let suppressNextClick = false;

    gallery.querySelectorAll('[data-product-video-stage]').forEach((stage) => {
        const video = stage.querySelector('[data-product-video]');
        const playButton = stage.querySelector('[data-product-video-play]');
        if (! video || ! playButton) return;

        const syncPlaybackState = () => {
            const playing = ! video.paused && ! video.ended;
            stage.classList.toggle('is-playing', playing);
            playButton.setAttribute('aria-label', `${playing ? 'Pause' : 'Play'} product video`);
        };

        const syncOrientation = () => {
            const portrait = video.videoHeight > video.videoWidth;
            stage.classList.toggle('is-portrait', portrait);
            stage.classList.toggle('is-landscape', ! portrait);
        };

        playButton.addEventListener('click', () => {
            if (video.paused || video.ended) video.play().catch(() => {});
            else video.pause();
        });
        video.addEventListener('loadedmetadata', syncOrientation, {once: true});
        video.addEventListener('play', syncPlaybackState);
        video.addEventListener('pause', syncPlaybackState);
        video.addEventListener('ended', syncPlaybackState);
        syncPlaybackState();
    });

    const normalize = (value) => (value + slides.length) % slides.length;
    const show = (value) => {
        if (!slides.length) return;
        index = normalize(value);
        track.style.transform = `translateX(-${index * 100}%)`;
        if (currentLabel) currentLabel.textContent = String(index + 1);
        thumbnails.forEach((thumbnail, position) => {
            const active = position === index;
            thumbnail.classList.toggle('is-active', active);
            thumbnail.setAttribute('aria-current', active ? 'true' : 'false');
        });
        slides.forEach((slide, position) => {
            if (position !== index) slide.querySelector('video')?.pause();
        });
        if (zoomHint) zoomHint.hidden = slides[index].dataset.mediaType !== 'image';
    };

    gallery.addEventListener('product:select-image', (event) => {
        const requested = event.detail?.image;
        if (! requested) return;
        const position = slides.findIndex((slide) => slide.dataset.originalSrc === requested);
        if (position >= 0) show(position);
    });

    const updateLightbox = () => {
        if (!lightboxImage) return;
        lightboxImage.src = slides[index].dataset.imageSrc;
        lightboxImage.alt = slides[index].querySelector('img')?.alt || 'Product photo';
        const imagePosition = imageSlideIndexes.indexOf(index);
        if (lightboxCounter) lightboxCounter.textContent = `${imagePosition + 1} / ${imageSlideIndexes.length}`;
    };

    const moveLightbox = (direction) => {
        if (! imageSlideIndexes.length) return;
        const current = imageSlideIndexes.indexOf(index);
        const nextImage = (current + direction + imageSlideIndexes.length) % imageSlideIndexes.length;
        show(imageSlideIndexes[nextImage]);
        updateLightbox();
    };

    const openLightbox = (position) => {
        show(position);
        updateLightbox();
        dialog?.showModal();
    };

    previous?.addEventListener('click', () => show(index - 1));
    next?.addEventListener('click', () => show(index + 1));
    thumbnails.forEach((thumbnail) => thumbnail.addEventListener('click', () => show(Number(thumbnail.dataset.productGalleryThumbnail))));
    slides.forEach((slide, position) => slide.querySelector('[data-open-product-image]')?.addEventListener('click', () => {
        if (suppressNextClick) {
            suppressNextClick = false;
            return;
        }
        openLightbox(position);
    }));

    viewport?.addEventListener('touchstart', (event) => {
        touchStartX = event.target.closest('video') ? null : event.changedTouches[0].clientX;
    }, {passive: true});
    viewport?.addEventListener('touchend', (event) => {
        if (touchStartX === null) return;
        const distance = event.changedTouches[0].clientX - touchStartX;
        if (Math.abs(distance) > 45) {
            suppressNextClick = true;
            show(index + (distance < 0 ? 1 : -1));
        }
        touchStartX = null;
    }, {passive: true});

    dialog?.querySelector('[data-product-lightbox-close]')?.addEventListener('click', () => dialog.close());
    dialog?.querySelector('[data-product-lightbox-previous]')?.addEventListener('click', () => moveLightbox(-1));
    dialog?.querySelector('[data-product-lightbox-next]')?.addEventListener('click', () => moveLightbox(1));
    dialog?.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    dialog?.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') moveLightbox(-1);
        if (event.key === 'ArrowRight') moveLightbox(1);
    });

    if (imageSlideIndexes.length < 2) {
        dialog?.querySelectorAll('[data-product-lightbox-previous], [data-product-lightbox-next]').forEach((button) => {
            button.hidden = true;
        });
    }

    show(0);
});
