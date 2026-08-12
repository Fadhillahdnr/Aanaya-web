import { mapProgressToTime } from './MusicSceneConfig';

export class MusicVideoScrubber {
    constructor(video, cues) {
        this.video = video;
        this.cues = cues;
        this.targetTime = 0;
        this.frame = null;
        this.loaded = false;
    }

    load() {
        if (this.loaded) return;
        const useMobile = window.matchMedia('(max-width: 767px)').matches;
        this.video.src = useMobile && this.video.dataset.mobileSrc
            ? this.video.dataset.mobileSrc
            : this.video.dataset.videoSrc;
        this.video.load();
        this.loaded = true;
        this.video.addEventListener('loadedmetadata', () => this.video.classList.add('is-ready'), { once: true });
        this.video.addEventListener('error', () => this.video.classList.add('is-failed'), { once: true });
    }

    seek(progress) {
        if (!this.loaded || this.video.readyState < 1) return;
        this.targetTime = Math.min(mapProgressToTime(progress, this.cues), Math.max(0, this.video.duration - .04));
        if (Math.abs(this.video.currentTime - this.targetTime) < .035 || this.frame) return;
        this.frame = requestAnimationFrame(() => {
            this.frame = null;
            if (Math.abs(this.video.currentTime - this.targetTime) >= .035) this.video.currentTime = this.targetTime;
        });
    }

    destroy() {
        if (this.frame) cancelAnimationFrame(this.frame);
        this.video.removeAttribute('src');
        this.video.load();
    }
}
