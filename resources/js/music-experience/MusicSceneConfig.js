export const MUSIC_SCENES = {
    unfoldHero: { cues: [[0, 0], [.08, .55], [.15, 1.15], [.23, 1.85], [.32, 2.75], [.42, 3.75], [.52, 4.85], [.61, 5.85], [.69, 6.65], [.75, 7.25], [.84, 7.8], [.91, 8.45], [.96, 9.1], [1, 9.72]] },
    unfoldPortal: { cues: [[0, 0], [.07, .4], [.14, 1], [.21, 1.8], [.28, 2.6], [.34, 3.3], [.4, 4], [.5, 4.7], [.62, 5.5], [.74, 6.3], [.84, 7.1], [.91, 7.9], [.96, 8.6], [1, 9.35]] },
    msyl: { cues: [[0, 0], [.08, .55], [.16, 1.3], [.25, 2.2], [.34, 3.1], [.43, 4], [.52, 4.9], [.6, 5.55], [.66, 5.85], [.7, 5.85], [.79, 6.8], [.87, 7.7], [.92, 8.4], [.95, 8.4], [1, 9.45]] },
    ending: { cues: [[0, 0], [.1, .8], [.2, 1.7], [.3, 2.7], [.4, 3.7], [.5, 4.7], [.6, 5.7], [.72, 6.6], [.82, 7.3], [.89, 8], [.94, 8.65], [1, 9.72]] },
};

export function mapProgressToTime(progress, cues) {
    const bounded = Math.min(1, Math.max(0, progress));
    for (let index = 1; index < cues.length; index += 1) {
        const [nextProgress, nextTime] = cues[index];
        if (bounded <= nextProgress) {
            const [previousProgress, previousTime] = cues[index - 1];
            const span = nextProgress - previousProgress || 1;
            return previousTime + ((bounded - previousProgress) / span) * (nextTime - previousTime);
        }
    }
    return cues.at(-1)[1];
}
