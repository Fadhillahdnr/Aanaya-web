export class MusicAudioReactive {
    constructor(root, audio) {
        this.root = root;
        this.audio = audio;
        this.canvas = root.querySelector('[data-music-visualizer]');
        this.frame = null;
        this.enabled = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    async connect() {
        if (!this.enabled || this.analyser) return;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            this.context = new AudioContext();
            this.source = this.context.createMediaElementSource(this.audio);
            this.analyser = this.context.createAnalyser();
            this.analyser.fftSize = 256;
            this.source.connect(this.analyser);
            this.analyser.connect(this.context.destination);
            this.frequency = new Uint8Array(this.analyser.frequencyBinCount);
            this.wave = new Uint8Array(this.analyser.fftSize);
        } catch {
            this.enabled = false;
        }
    }

    start() {
        if (!this.enabled || !this.analyser || this.frame || document.hidden) return;
        const render = () => {
            if (this.audio.paused || document.hidden) { this.frame = null; return; }
            this.analyser.getByteFrequencyData(this.frequency);
            const average = (start, end) => this.frequency.slice(start, end).reduce((sum, value) => sum + value, 0) / Math.max(1, end - start) / 255;
            const low = average(0, 12); const mid = average(12, 45); const high = average(45, 100);
            this.root.style.setProperty('--music-low', low.toFixed(3));
            this.root.style.setProperty('--music-mid', mid.toFixed(3));
            this.root.style.setProperty('--music-high', high.toFixed(3));
            this.root.style.setProperty('--music-energy', ((low + mid + high) / 3).toFixed(3));
            this.drawWave();
            this.frame = requestAnimationFrame(render);
        };
        this.frame = requestAnimationFrame(render);
    }

    drawWave() {
        if (!this.canvas) return;
        const ratio = Math.min(window.devicePixelRatio || 1, 1.5);
        const width = Math.max(1, this.canvas.clientWidth); const height = Math.max(1, this.canvas.clientHeight);
        if (this.canvas.width !== width * ratio) { this.canvas.width = width * ratio; this.canvas.height = height * ratio; }
        const context = this.canvas.getContext('2d');
        this.analyser.getByteTimeDomainData(this.wave);
        context.clearRect(0, 0, this.canvas.width, this.canvas.height);
        context.strokeStyle = 'rgba(252,248,237,.82)'; context.lineWidth = 1.5 * ratio; context.beginPath();
        this.wave.forEach((value, index) => {
            const x = index / (this.wave.length - 1) * this.canvas.width;
            const y = (value / 255) * this.canvas.height;
            index ? context.lineTo(x, y) : context.moveTo(x, y);
        });
        context.stroke();
    }

    stop() { if (this.frame) cancelAnimationFrame(this.frame); this.frame = null; }
    destroy() { this.stop(); this.source?.disconnect(); this.analyser?.disconnect(); this.context?.close().catch(() => {}); }
}
