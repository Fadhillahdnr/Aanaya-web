const cue = (progress, time) => ({ progress, time });

export const ARTICLE_SCENES = {
    reading: {
        audio: { offset: 0 },
        source: '/videos/article-experience/optimized/scene-reading.mp4',
        mobileSource: '/videos/article-experience/optimized/mobile/scene-reading.mp4',
        fallbackSource: '/videos/article-experience/source/video-1.mp4',
        desktopScrollVh: 560,
        mobileScrollVh: 330,
        cues: [cue(0, 0), cue(.10, .45), cue(.17, .45), cue(.35, 2.6), cue(.48, 4.2), cue(.60, 5.5), cue(.74, 7.25), cue(.84, 7.55), cue(.91, 7.55), cue(1, 9.82)],
    },
    'approach-book': {
        audio: { offset: 10 },
        source: '/videos/article-experience/optimized/scene-approach-book.mp4',
        mobileSource: '/videos/article-experience/optimized/mobile/scene-approach-book.mp4',
        fallbackSource: '/videos/article-experience/source/video-2.mp4',
        desktopScrollVh: 610,
        mobileScrollVh: 350,
        cues: [cue(0, 1.3), cue(.10, 1.6), cue(.18, 1.6), cue(.40, 3.6), cue(.52, 4.6), cue(.60, 4.6), cue(.76, 6.9), cue(.88, 8.1), cue(.94, 8.1), cue(1, 9.82)],
    },
    'enter-book': {
        audio: { offset: 20 },
        source: '/videos/article-experience/optimized/scene-enter-book.mp4',
        mobileSource: '/videos/article-experience/optimized/mobile/scene-enter-book.mp4',
        fallbackSource: '/videos/article-experience/source/video-4.mp4',
        desktopScrollVh: 610,
        mobileScrollVh: 350,
        cues: [cue(0, 0), cue(.14, .8), cue(.26, 2.4), cue(.34, 2.8), cue(.40, 2.8), cue(.64, 5.8), cue(.76, 7.1), cue(.84, 7.6), cue(.92, 7.6), cue(1, 9.82)],
    },
    'paper-plane': {
        audio: { offset: 30 },
        source: '/videos/article-experience/optimized/scene-paper-plane.mp4',
        mobileSource: '/videos/article-experience/optimized/mobile/scene-paper-plane.mp4',
        fallbackSource: '/videos/article-experience/source/video-3.mp4',
        desktopScrollVh: 660,
        mobileScrollVh: 380,
        cues: [cue(0, 0), cue(.14, 1.4), cue(.24, 2.8), cue(.31, 3.5), cue(.39, 3.5), cue(.58, 5.7), cue(.69, 6.9), cue(.77, 7.45), cue(.84, 7.45), cue(.94, 9.1), cue(1, 9.82)],
    },
    ending: {
        audio: { offset: 40 },
        source: '/videos/article-experience/optimized/scene-ending.mp4',
        mobileSource: '/videos/article-experience/optimized/mobile/scene-ending.mp4',
        fallbackSource: '/videos/article-experience/source/video-5.mp4',
        desktopScrollVh: 560,
        mobileScrollVh: 330,
        finalHoldVh: 125,
        cues: [cue(0, 0), cue(.15, 1.3), cue(.30, 3.1), cue(.39, 4.35), cue(.58, 6.3), cue(.68, 7.1), cue(.78, 7.45), cue(.84, 7.45), cue(.95, 9.4), cue(1, 9.82)],
    },
};

export const AUDIO_CONFIG = {
    src: '/audio/article-experience/aanaya-cinematic-story-soundtrack-50s.mp3',
    duration: 50,
    masterGain: .72,
    reverseGainMultiplier: .68,
    desktopIdleDelayMs: 135,
    mobileIdleDelayMs: 190,
    fadeInMs: 70,
    fadeOutMs: 55,
    driftSoftThreshold: .08,
    driftHardThreshold: .18,
    minimumPlaybackRate: .75,
    maximumPlaybackRate: 1.35,
};

export function clamp(value, minimum = 0, maximum = 1) {
    return Math.min(maximum, Math.max(minimum, Number.isFinite(value) ? value : minimum));
}

export function mapProgressToTime(progress, cues, duration = Infinity) {
    if (!Array.isArray(cues) || cues.length === 0) return 0;

    const safeProgress = clamp(progress);
    const lastCue = cues[cues.length - 1];
    let mappedTime = lastCue.time;

    for (let index = 0; index < cues.length - 1; index += 1) {
        const from = cues[index];
        const to = cues[index + 1];
        if (safeProgress > to.progress) continue;

        const span = to.progress - from.progress;
        const segmentProgress = span <= 0 ? 1 : clamp((safeProgress - from.progress) / span);
        mappedTime = from.time + ((to.time - from.time) * segmentProgress);
        break;
    }

    const safeDuration = Number.isFinite(duration) && duration > 0 ? Math.max(0, duration - .04) : Infinity;
    return clamp(mappedTime, 0, safeDuration);
}
