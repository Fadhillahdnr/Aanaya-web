import { ARTICLE_SCENES, AUDIO_CONFIG, clamp, mapProgressToTime } from './ArticleSceneConfig';

const SEEK_EPSILON_SECONDS = .03;
const PROGRESS_EPSILON = .0005;
const SEEK_SMOOTHING = .18;

export class ArticleVideo {
    constructor(root, ScrollTrigger, prefersReducedMotion, onSceneProgress = () => {}) {
        this.root = root;
        this.ScrollTrigger = ScrollTrigger;
        this.prefersReducedMotion = prefersReducedMotion;
        this.onSceneProgress = onSceneProgress;
        this.sceneStates = [];
    }

    init() {
        if (this.prefersReducedMotion) return;

        this.preloadObserver = new IntersectionObserver((entries) => entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const state = this.sceneStates.find((candidate) => candidate.scene === entry.target);
            if (state) this.loadVideo(state);
            this.preloadObserver.unobserve(entry.target);
        }), { rootMargin: '140% 0px', threshold: 0 });

        this.root.querySelectorAll('[data-video-scene]').forEach((scene, index) => {
            const sceneId = scene.dataset.scene;
            const config = ARTICLE_SCENES[sceneId];
            const video = scene.querySelector('[data-scrub-video]');
            if (!config || !video) return;

            const isMobile = window.matchMedia('(max-width: 767px)').matches;
            const scrubVh = isMobile ? config.mobileScrollVh : config.desktopScrollVh;
            const holdVh = config.finalHoldVh || 0;
            scene.style.setProperty('--scene-scroll', `${scrubVh + holdVh}vh`);

            const optimizedAvailable = isMobile
                ? this.root.dataset.mobileOptimizedVideos === 'true'
                : this.root.dataset.optimizedVideos === 'true';
            const preferredSource = optimizedAvailable
                ? (isMobile ? config.mobileSource : config.source)
                : config.fallbackSource;

            const state = {
                scene, sceneId, config, video, preferredSource,
                duration: 0, targetProgress: 0, smoothedProgress: 0,
                previousMappedTime: null,
                scrubFraction: scrubVh / (scrubVh + holdVh),
                seekFrame: null, trigger: null, loaded: false, fallbackTried: false, listeners: [],
            };

            this.sceneStates.push(state);
            this.bindVideoEvents(state);
            this.preloadObserver.observe(scene);
            if (index === 0) this.loadVideo(state);
        });
    }

    bindVideoEvents(state) {
        const onMetadata = () => {
            if (!Number.isFinite(state.video.duration) || state.video.duration <= 0) return;
            state.duration = state.video.duration;
            state.scene.classList.add('is-video-ready');
            state.scene.querySelector('[data-cinematic-media]')?.classList.add('is-video-ready');
            this.createScrollScrub(state);
            this.ScrollTrigger.refresh();
        };
        const onError = () => {
            if (!state.fallbackTried && state.video.src !== new URL(state.config.fallbackSource, window.location.href).href) {
                state.fallbackTried = true;
                this.setSource(state, state.config.fallbackSource);
                return;
            }
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
        if (state.loaded) return;
        state.loaded = true;
        this.setSource(state, state.preferredSource);
    }

    setSource(state, source) {
        state.video.src = source;
        state.video.preload = 'auto';
        state.video.load();
    }

    createScrollScrub(state) {
        if (state.trigger) return;
        state.trigger = this.ScrollTrigger.create({
            trigger: state.scene,
            start: 'top top',
            end: 'bottom bottom',
            invalidateOnRefresh: true,
            onUpdate: (trigger) => this.queueSeek(state, trigger.progress, trigger.getVelocity()),
            onLeave: () => state.video.pause(),
            onLeaveBack: () => state.video.pause(),
        });
    }

    queueSeek(state, overallProgress, velocity = 0) {
        state.targetProgress = clamp(overallProgress / state.scrubFraction);
        const mappedVideoTime = mapProgressToTime(state.targetProgress, state.config.cues, state.duration);
        const mappedTimeDelta = state.previousMappedTime === null ? 0 : mappedVideoTime - state.previousMappedTime;
        const isHolding = overallProgress > state.scrubFraction || Math.abs(mappedTimeDelta) < .003;
        state.previousMappedTime = mappedVideoTime;
        this.onSceneProgress(state.sceneId, {
            progress: overallProgress,
            videoProgress: state.targetProgress,
            mappedVideoTime,
            globalAudioTime: clamp(state.config.audio.offset + mappedVideoTime, 0, AUDIO_CONFIG.duration),
            direction: mappedTimeDelta >= 0 ? 1 : -1,
            velocity,
            moving: !isHolding,
            isHolding,
        });
        if (state.seekFrame === null) state.seekFrame = requestAnimationFrame(() => this.updateSeek(state));
    }

    updateSeek(state) {
        state.seekFrame = null;
        const difference = state.targetProgress - state.smoothedProgress;
        state.smoothedProgress += difference * SEEK_SMOOTHING;
        if (Math.abs(difference) <= PROGRESS_EPSILON) state.smoothedProgress = state.targetProgress;

        const targetTime = mapProgressToTime(state.smoothedProgress, state.config.cues, state.duration);
        if (!state.video.seeking && Math.abs(state.video.currentTime - targetTime) >= SEEK_EPSILON_SECONDS) {
            try { state.video.currentTime = targetTime; } catch { state.scene.classList.add('is-video-failed'); return; }
        }
        if (state.smoothedProgress !== state.targetProgress || state.video.seeking) {
            state.seekFrame = requestAnimationFrame(() => this.updateSeek(state));
        }
    }

    destroy() {
        this.preloadObserver?.disconnect();
        this.sceneStates.forEach((state) => {
            state.trigger?.kill();
            state.listeners.forEach((remove) => remove());
            state.video.pause();
            if (state.seekFrame !== null) cancelAnimationFrame(state.seekFrame);
        });
        this.sceneStates = [];
    }
}
