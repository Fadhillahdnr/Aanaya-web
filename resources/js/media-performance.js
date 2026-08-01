const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const saveData = navigator.connection?.saveData === true;

const loadVideo = (video) => {
    if (video.dataset.loaded === 'true') return;
    video.querySelectorAll('source[data-src]').forEach((source) => {
        source.src = source.dataset.src;
    });
    video.dataset.loaded = 'true';
    video.load();
};

document.addEventListener('DOMContentLoaded', () => {
    const videos = [...document.querySelectorAll('video[data-lazy-video]')];
    if (!videos.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(({isIntersecting, target: video}) => {
            if (isIntersecting) {
                loadVideo(video);
                if (!reducedMotion && !saveData && video.dataset.autoplay === 'true') {
                    video.play().catch(() => {});
                }
            } else if (!video.paused) {
                video.pause();
            }
        });
    }, {rootMargin: '200px 0px', threshold: 0.15});

    videos.forEach((video) => observer.observe(video));
});
