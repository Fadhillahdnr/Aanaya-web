import { AUDIO_CONFIG, clamp } from './ArticleSceneConfig';

const STORAGE_KEY = 'aanaya-cinematic-sound';
const MINIMUM_MOVEMENT_SECONDS = .006;

export class ArticleAudio {
    constructor(root, prefersReducedMotion) {
        this.root = root;
        this.prefersReducedMotion = prefersReducedMotion;
        this.button = root.querySelector('[data-article-sound]');
        this.label = root.querySelector('[data-sound-state]');
        this.enabled = false;
        this.isLoading = false;
        this.isUnavailable = false;
        this.targetTime = null;
        this.previousTargetTime = null;
        this.direction = 1;
        this.activeVoice = null;
        this.idleTimer = null;
        this.lastUpdateAt = 0;
    }

    init() {
        if (!this.button) return;
        this.AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (this.prefersReducedMotion || !this.AudioContextClass) {
            this.markUnavailable(this.prefersReducedMotion ? 'Sound unavailable in reduced motion' : 'Sound unavailable');
            return;
        }

        this.onToggle = () => this.toggle();
        this.onVisibilityChange = () => {
            if (document.hidden) this.pauseImmediately();
        };
        this.button.addEventListener('click', this.onToggle);
        document.addEventListener('visibilitychange', this.onVisibilityChange);
        this.render();
    }

    async toggle() {
        if (this.isLoading || this.isUnavailable) return;
        if (this.enabled) {
            this.enabled = false;
            localStorage.setItem(STORAGE_KEY, 'off');
            this.pauseImmediately();
            this.render();
            return;
        }

        this.isLoading = true;
        this.render();
        try {
            await this.ensureAudioReady();
            await this.context.resume();
            this.enabled = true;
            localStorage.setItem(STORAGE_KEY, 'on');
        } catch (error) {
            this.markUnavailable('Cinematic sound unavailable');
            console.warn('Cinematic soundtrack could not be initialized.', error);
        } finally {
            this.isLoading = false;
            this.render();
        }
    }

    async ensureAudioReady() {
        if (this.forwardBuffer && this.reverseBuffer) return;
        this.context ||= new this.AudioContextClass({ latencyHint: 'interactive' });
        this.masterGain ||= this.context.createGain();
        this.masterGain.gain.value = AUDIO_CONFIG.masterGain;
        this.masterGain.connect(this.context.destination);

        const response = await fetch(AUDIO_CONFIG.src, { credentials: 'same-origin' });
        if (!response.ok) throw new Error(`Soundtrack request failed (${response.status}).`);
        this.forwardBuffer = await this.context.decodeAudioData(await response.arrayBuffer());
        this.reverseBuffer = this.createReverseBuffer(this.forwardBuffer);
    }

    createReverseBuffer(sourceBuffer) {
        const reverseBuffer = this.context.createBuffer(
            sourceBuffer.numberOfChannels,
            sourceBuffer.length,
            sourceBuffer.sampleRate,
        );
        for (let channel = 0; channel < sourceBuffer.numberOfChannels; channel += 1) {
            const reversedSamples = Float32Array.from(sourceBuffer.getChannelData(channel));
            reversedSamples.reverse();
            reverseBuffer.copyToChannel(reversedSamples, channel);
        }
        return reverseBuffer;
    }

    setTargetTime({ time, moving }) {
        const now = performance.now();
        const safeTime = clamp(time, 0, Math.min(AUDIO_CONFIG.duration, this.forwardBuffer?.duration || AUDIO_CONFIG.duration));
        const delta = this.previousTargetTime === null ? 0 : safeTime - this.previousTargetTime;
        const hasMoved = moving && Math.abs(delta) >= MINIMUM_MOVEMENT_SECONDS;

        this.targetTime = safeTime;
        this.previousTargetTime = safeTime;
        if (!hasMoved) {
            if (this.activeVoice && this.idleTimer === null) this.scheduleIdlePause();
            return;
        }

        const nextDirection = delta >= 0 ? 1 : -1;
        const elapsedSeconds = Math.max((now - this.lastUpdateAt) / 1000, .001);
        const requestedRate = clamp(Math.abs(delta) / elapsedSeconds, AUDIO_CONFIG.minimumPlaybackRate, AUDIO_CONFIG.maximumPlaybackRate);
        this.lastUpdateAt = now;
        this.direction = nextDirection;
        this.scheduleIdlePause();

        if (!this.enabled || !this.forwardBuffer || document.hidden) return;
        if (!this.activeVoice || this.activeVoice.direction !== nextDirection) {
            this.startVoice(safeTime, nextDirection, requestedRate);
            return;
        }

        const drift = this.calculateDrift(safeTime);
        if (Math.abs(drift) > AUDIO_CONFIG.driftHardThreshold) {
            this.startVoice(safeTime, nextDirection, requestedRate);
        } else if (Math.abs(drift) > AUDIO_CONFIG.driftSoftThreshold) {
            const correction = nextDirection * drift > 0 ? .1 : -.1;
            const correctedRate = clamp(requestedRate + correction, AUDIO_CONFIG.minimumPlaybackRate, AUDIO_CONFIG.maximumPlaybackRate);
            this.activeVoice.source.playbackRate.setTargetAtTime(
                correctedRate,
                this.context.currentTime,
                .025,
            );
            this.activeVoice.rate = correctedRate;
        } else {
            this.activeVoice.source.playbackRate.setTargetAtTime(requestedRate, this.context.currentTime, .035);
            this.activeVoice.rate = requestedRate;
        }
    }

    calculateDrift(targetTime) {
        if (!this.activeVoice) return Infinity;
        const elapsed = (this.context.currentTime - this.activeVoice.startedAt) * this.activeVoice.rate;
        const expectedTime = this.activeVoice.direction > 0
            ? this.activeVoice.timelineStart + elapsed
            : this.activeVoice.timelineStart - elapsed;
        return targetTime - expectedTime;
    }

    startVoice(timelineTime, direction, playbackRate) {
        if (!this.context || this.context.state !== 'running') return;
        const fadeInSeconds = AUDIO_CONFIG.fadeInMs / 1000;
        const source = this.context.createBufferSource();
        const gain = this.context.createGain();
        const buffer = direction > 0 ? this.forwardBuffer : this.reverseBuffer;
        const offset = direction > 0 ? timelineTime : buffer.duration - timelineTime;
        const safeOffset = clamp(offset, 0, Math.max(0, buffer.duration - .01));
        const voiceGain = direction > 0 ? 1 : AUDIO_CONFIG.reverseGainMultiplier;

        source.buffer = buffer;
        source.playbackRate.value = playbackRate;
        gain.gain.setValueAtTime(0, this.context.currentTime);
        gain.gain.linearRampToValueAtTime(voiceGain, this.context.currentTime + fadeInSeconds);
        source.connect(gain).connect(this.masterGain);
        source.start(0, safeOffset);

        this.fadeOutVoice(this.activeVoice);
        this.activeVoice = {
            source, gain, direction, rate: playbackRate,
            timelineStart: timelineTime,
            startedAt: this.context.currentTime,
        };
        source.onended = () => {
            if (this.activeVoice?.source === source) this.activeVoice = null;
            source.disconnect();
            gain.disconnect();
        };
    }

    scheduleIdlePause() {
        clearTimeout(this.idleTimer);
        const delay = window.matchMedia('(max-width: 767px)').matches
            ? AUDIO_CONFIG.mobileIdleDelayMs
            : AUDIO_CONFIG.desktopIdleDelayMs;
        this.idleTimer = window.setTimeout(() => this.pauseImmediately(), delay);
    }

    pauseImmediately() {
        clearTimeout(this.idleTimer);
        this.idleTimer = null;
        this.fadeOutVoice(this.activeVoice);
        this.activeVoice = null;
    }

    fadeOutVoice(voice) {
        if (!voice || !this.context) return;
        const now = this.context.currentTime;
        const fadeSeconds = AUDIO_CONFIG.fadeOutMs / 1000;
        voice.gain.gain.cancelScheduledValues(now);
        voice.gain.gain.setValueAtTime(voice.gain.gain.value, now);
        voice.gain.gain.linearRampToValueAtTime(0, now + fadeSeconds);
        try { voice.source.stop(now + fadeSeconds + .01); } catch {}
    }

    markUnavailable(label) {
        this.isUnavailable = true;
        this.button.disabled = true;
        this.button.setAttribute('aria-label', label);
        this.render();
    }

    render() {
        if (!this.button || !this.label) return;
        this.button.setAttribute('aria-pressed', String(this.enabled));
        this.button.setAttribute('aria-label', this.isUnavailable
            ? 'Cinematic sound unavailable'
            : this.enabled ? 'Disable cinematic sound' : 'Enable cinematic sound');
        this.button.classList.toggle('is-loading', this.isLoading);
        this.label.textContent = this.isUnavailable ? 'Unavailable' : this.isLoading ? 'Loading' : this.enabled ? 'On' : 'Off';
    }

    destroy() {
        this.button?.removeEventListener('click', this.onToggle);
        document.removeEventListener('visibilitychange', this.onVisibilityChange);
        this.pauseImmediately();
        this.masterGain?.disconnect();
        this.context?.close().catch(() => {});
        this.forwardBuffer = null;
        this.reverseBuffer = null;
    }
}
