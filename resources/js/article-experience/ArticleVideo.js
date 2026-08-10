const SEEK_EPSILON_SECONDS = 0.035;
const PROGRESS_EPSILON = 0.0005;
const SEEK_SMOOTHING = 0.1;

export class ArticleVideo {
    constructor(root, ScrollTrigger, prefersReducedMotion) {
        this.root = root;
        this.ScrollTrigger = ScrollTrigger;
        this.prefersReducedMotion = prefersReducedMotion;
        this.sceneStates = [];
        this.preloadObserver = null;
    }

    init() {
        const scenes = Array.from(this.root.querySelectorAll('[data-video-scene]'));

        if (!scenes.length || this.prefersReducedMotion) {
            return;
        }

        this.preloadObserver = new IntersectionObserver(
            (entries) => entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                const state = this.sceneStates.find((candidate) => candidate.scene === entry.target);
                if (state) {
                    this.loadVideo(state);
                    this.preloadObserver.unobserve(entry.target);
                }
            }),
            { rootMargin: '120% 0px', threshold: 0 },
        );

        scenes.forEach((scene, index) => {
            const video = scene.querySelector('[data-scrub-video]');

            if (!video) {
                return;
            }

            const state = {
                scene,
                video,
                duration: 0,
                targetProgress: 0,
                smoothedProgress: 0,
                seekFrame: null,
                trigger: null,
                loaded: false,
                listeners: [],
            };

            this.sceneStates.push(state);
            this.bindVideoEvents(state);
            this.preloadObserver.observe(scene);

            if (index === 0) {
                this.loadVideo(state);
            }
        });
    }

    bindVideoEvents(state) {
        const onMetadata = () => {
            if (!Number.isFinite(state.video.duration) || state.video.duration <= 0) {
                return;
            }

            state.duration = state.video.duration;
            state.scene.classList.add('is-video-ready');
            state.video.closest('[data-cinematic-media]')?.classList.add('is-video-ready');
            this.createScrollScrub(state);
            this.ScrollTrigger.refresh();
        };
        const onError = () => {
            state.scene.classList.add('is-video-failed');
            state.scene.classList.remove('is-video-ready');
        };

        state.video.addEventListener('loadedmetadata', onMetadata);
        state.video.addEventListener('error', onError);
        state.listeners.push(
            () => state.video.removeEventListener('loadedmetadata', onMetadata),
            () => state.video.removeEventListener('error', onError),
        );
    }

    loadVideo(state) {
        if (state.loaded) {
            return;
        }

        const source = state.video.dataset.videoSrc;
        if (!source) {
            state.scene.classList.add('is-video-failed');
            return;
        }

        state.loaded = true;
        state.video.src = source;
        state.video.preload = 'auto';
        state.video.load();
    }

    createScrollScrub(state) {
        if (state.trigger) {
            return;
        }

        state.trigger = this.ScrollTrigger.create({
            trigger: state.scene,
            start: 'top top',
            end: 'bottom bottom',
            invalidateOnRefresh: true,
            onUpdate: ({ progress }) => this.queueSeek(state, progress),
            onLeave: () => state.video.pause(),
            onLeaveBack: () => state.video.pause(),
        });
    }

    queueSeek(state, progress) {
        state.targetProgress = Math.max(0, Math.min(1, progress));

        if (state.seekFrame !== null) {
            return;
        }

        state.seekFrame = requestAnimationFrame(() => this.updateSeek(state));
    }

    updateSeek(state) {
        state.seekFrame = null;

        const progressDifference = state.targetProgress - state.smoothedProgress;
        state.smoothedProgress += progressDifference * SEEK_SMOOTHING;

        if (Math.abs(progressDifference) <= PROGRESS_EPSILON) {
            state.smoothedProgress = state.targetProgress;
        }

        const smoothedTime = Math.max(
            0,
            Math.min(state.duration - 0.04, state.duration * state.smoothedProgress),
        );

        if (!state.video.seeking && Math.abs(state.video.currentTime - smoothedTime) >= SEEK_EPSILON_SECONDS) {
            try {
                state.video.currentTime = smoothedTime;
            } catch {
                state.scene.classList.add('is-video-failed');
                return;
            }
        }

        if (state.smoothedProgress !== state.targetProgress || state.video.seeking) {
            state.seekFrame = requestAnimationFrame(() => this.updateSeek(state));
        }
    }

    destroy() {
        this.preloadObserver?.disconnect();

        this.sceneStates.forEach((state) => {
            state.trigger?.kill();
            state.listeners.forEach((removeListener) => removeListener());
            state.video.pause();
            if (state.seekFrame !== null) cancelAnimationFrame(state.seekFrame);
        });

        this.sceneStates = [];
    }
}
