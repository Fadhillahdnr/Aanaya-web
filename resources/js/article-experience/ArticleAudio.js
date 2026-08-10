import { ARTICLE_SCENES } from './ArticleSceneConfig';

const STORAGE_KEY = 'aanaya.article.sound';
const AUDIO_ROOT = '/audio/article-experience/';

export class ArticleAudio {
    constructor(root) {
        this.root = root;
        this.button = root.querySelector('[data-article-sound]');
        this.label = root.querySelector('[data-sound-state]');
        this.enabled = false;
        this.audio = new Map();
        this.cueState = new Map();
        this.lastProgress = new Map();
        this.lastPlayedAt = new Map();
    }

    init() {
        if (!this.button) return;
        this.onToggle = () => this.setEnabled(!this.enabled, true);
        this.button.addEventListener('click', this.onToggle);
        this.render();
    }

    setEnabled(enabled, persist = false) {
        this.enabled = enabled;
        if (persist) localStorage.setItem(STORAGE_KEY, enabled ? 'on' : 'off');
        if (!enabled) this.audio.forEach((sound) => { sound.pause(); sound.currentTime = 0; });
        this.render();
    }

    render() {
        if (!this.button || !this.label) return;
        this.button.setAttribute('aria-pressed', String(this.enabled));
        this.label.textContent = this.enabled ? 'On' : 'Off';
    }

    updateScene(sceneId, progress) {
        if (!this.enabled) return;
        const previous = this.lastProgress.get(sceneId) ?? progress;
        this.lastProgress.set(sceneId, progress);
        if (progress < previous) return;

        (ARTICLE_SCENES[sceneId]?.audioCues || []).forEach((cue) => {
            const key = `${sceneId}:${cue.id}`;
            if (progress < cue.progress - .035) this.cueState.delete(key);
            if (this.cueState.has(key) || previous > cue.progress || progress < cue.progress) return;
            if (Date.now() - (this.lastPlayedAt.get(key) || 0) < 1200) return;
            this.cueState.set(key, true);
            this.lastPlayedAt.set(key, Date.now());
            this.play(cue.asset);
        });
    }

    play(asset) {
        let sound = this.audio.get(asset);
        if (!sound) {
            sound = new Audio(`${AUDIO_ROOT}${asset}`);
            sound.preload = 'none';
            sound.volume = .16;
            sound.addEventListener('error', () => this.audio.delete(asset), { once: true });
            this.audio.set(asset, sound);
        }
        sound.currentTime = 0;
        sound.play().catch(() => {});
    }

    destroy() {
        this.button?.removeEventListener('click', this.onToggle);
        this.audio.forEach((sound) => sound.pause());
        this.audio.clear();
    }
}
