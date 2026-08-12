import { MusicAudioReactive } from './MusicAudioReactive';

export class MusicPlayer {
    constructor(root, tracks) {
        this.root = root; this.tracks = tracks; this.currentIndex = -1; this.state = 'idle';
        this.player = root.querySelector('[data-music-player]'); this.audio = this.player.querySelector('[data-music-player-audio]');
        this.audio.volume = .8; this.reactive = new MusicAudioReactive(root, this.audio); this.cleanups = [];
    }

    init() {
        this.listen(this.root, 'click', (event) => { const button = event.target.closest('[data-play-track]'); if (button) this.select(Number(button.dataset.playTrack)); });
        this.listen(this.player.querySelector('[data-player-toggle]'), 'click', () => this.toggle());
        this.listen(this.player.querySelector('[data-player-previous]'), 'click', () => this.step(-1));
        this.listen(this.player.querySelector('[data-player-next]'), 'click', () => this.step(1));
        this.listen(this.player.querySelector('[data-player-seek]'), 'input', (event) => { if (Number.isFinite(this.audio.duration)) this.audio.currentTime = Number(event.target.value); });
        this.listen(this.player.querySelector('[data-player-volume]'), 'input', (event) => { this.audio.volume = Number(event.target.value); });
        this.listen(this.player.querySelector('[data-player-expand]'), 'click', (event) => { const expanded = this.player.classList.toggle('is-expanded'); event.currentTarget.setAttribute('aria-expanded', String(expanded)); });
        ['play', 'playing', 'pause', 'waiting', 'ended', 'error', 'loadedmetadata', 'timeupdate'].forEach((name) => this.listen(this.audio, name, () => this.sync(name)));
        this.listen(document, 'visibilitychange', () => document.hidden ? this.reactive.stop() : (!this.audio.paused && this.reactive.start()));
        this.initMediaSession();
    }

    async select(id) {
        const index = this.tracks.findIndex((track) => Number(track.id) === id);
        if (index < 0 || !this.tracks[index].audio) return;
        if (this.currentIndex === index) { await this.toggle(); return; }
        this.currentIndex = index; const track = this.tracks[index]; this.setState('loading', `Loading ${track.title}…`);
        this.audio.pause(); this.audio.crossOrigin = 'anonymous'; this.audio.src = track.audio; this.audio.load(); this.renderTrack(track);
        try { await this.reactive.connect(); await this.audio.play(); } catch { this.setState('error', `Could not play ${track.title}. Please try again.`); }
    }

    async toggle() {
        if (this.currentIndex < 0) { const first = this.tracks.find((track) => track.audio); if (first) await this.select(Number(first.id)); return; }
        if (this.audio.paused) { try { await this.audio.play(); } catch { this.setState('error', 'Playback could not start.'); } } else this.audio.pause();
    }

    step(direction) { if (!this.tracks.length) return; const next = (this.currentIndex + direction + this.tracks.length) % this.tracks.length; this.select(Number(this.tracks[next].id)); }

    sync(eventName) {
        if (eventName === 'timeupdate') {
            this.player.querySelector('[data-player-current]').textContent = this.format(this.audio.currentTime);
            if (!this.player.querySelector('[data-player-seek]').matches(':active')) this.player.querySelector('[data-player-seek]').value = this.audio.currentTime;
            if (!this.audio.paused && this.state === 'loading' && this.audio.currentTime > 0) {
                this.setState('playing', `Now playing ${this.tracks[this.currentIndex]?.title}`);
                this.reactive.start();
            }
            return;
        }
        if (eventName === 'loadedmetadata') { this.player.querySelector('[data-player-seek]').max = this.audio.duration || 0; this.player.querySelector('[data-player-duration]').textContent = this.format(this.audio.duration); }
        if (eventName === 'play' || eventName === 'playing') { this.setState('playing', `Now playing ${this.tracks[this.currentIndex]?.title}`); this.reactive.start(); }
        if (eventName === 'pause' && !this.audio.ended) { this.setState('paused', 'Playback paused'); this.reactive.stop(); }
        if (eventName === 'waiting' && this.audio.currentTime === 0) this.setState('loading', 'Loading audio…');
        if (eventName === 'ended') { this.setState('ended', 'Track ended'); this.reactive.stop(); }
        if (eventName === 'error') { this.setState('error', 'Playback failed. Try another release or streaming link.'); this.reactive.stop(); }
    }

    renderTrack(track) {
        this.player.hidden = false; this.root.classList.add('music-player-active');
        const cover = this.player.querySelector('[data-player-cover]'); cover.src = track.cover || ''; cover.alt = track.cover ? `Cover artwork ${track.title}` : '';
        this.player.querySelector('[data-player-title]').textContent = track.title; this.player.querySelector('[data-player-artist]').textContent = track.artist || 'Aanaya';
        document.querySelectorAll('[data-music-release]').forEach((release) => { const active = Number(release.dataset.musicRelease) === Number(track.id); release.classList.toggle('is-playing', active); release.querySelector('[data-playing-label]')?.toggleAttribute('hidden', !active); });
        if ('mediaSession' in navigator) navigator.mediaSession.metadata = new MediaMetadata({ title: track.title, artist: track.artist || 'Aanaya', artwork: track.cover ? [{ src: track.cover }] : [] });
    }

    setState(state, message) { this.state = state; this.player.dataset.state = state; this.player.querySelector('[data-player-status]').textContent = message; const playing = state === 'playing'; this.player.querySelector('[data-player-toggle]').innerHTML = `<span aria-hidden="true">${playing ? '❚❚' : '▶'}</span>`; this.player.querySelector('[data-player-toggle]').setAttribute('aria-label', playing ? 'Pause' : 'Play'); }
    initMediaSession() { if (!('mediaSession' in navigator)) return; navigator.mediaSession.setActionHandler('play', () => this.toggle()); navigator.mediaSession.setActionHandler('pause', () => this.toggle()); navigator.mediaSession.setActionHandler('previoustrack', () => this.step(-1)); navigator.mediaSession.setActionHandler('nexttrack', () => this.step(1)); navigator.mediaSession.setActionHandler('seekto', ({ seekTime }) => { if (Number.isFinite(seekTime)) this.audio.currentTime = seekTime; }); }
    format(seconds) { if (!Number.isFinite(seconds)) return '0:00'; return `${Math.floor(seconds / 60)}:${String(Math.floor(seconds % 60)).padStart(2, '0')}`; }
    listen(target, name, handler) { target?.addEventListener(name, handler); this.cleanups.push(() => target?.removeEventListener(name, handler)); }
    destroy() { this.cleanups.splice(0).forEach((cleanup) => cleanup()); this.audio.pause(); this.reactive.destroy(); }
}
