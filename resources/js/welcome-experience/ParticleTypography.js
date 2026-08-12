const clamp = (value, minimum, maximum) => Math.min(maximum, Math.max(minimum, value));

export class ParticleTypography {
    constructor(canvas, options = {}) {
        this.canvas = canvas;
        this.context = canvas.getContext('2d', { alpha: true });
        this.options = options;
        this.particles = [];
        this.pointer = { x: 0, y: 0, active: false };
        this.scrollProgress = 0;
        this.formationProgress = 0;
        this.visible = true;
        this.running = false;
        this.frame = null;
        this.resizeTimer = null;
        this.recoveryTimer = null;
        this.lastRenderAt = 0;
        this.lastWidth = 0;
        this.lastHeight = 0;
        this.boundRender = this.render.bind(this);
        this.boundPointerMove = this.handlePointerMove.bind(this);
        this.boundPointerLeave = () => { this.pointer.active = false; };
        this.boundResize = this.handleResize.bind(this);
        this.boundVisibility = this.handleVisibility.bind(this);
        this.boundPageShow = this.handlePageShow.bind(this);
    }

    async init() {
        if (!this.context) throw new Error('Canvas 2D is unavailable.');
        // Keep the canvas measurable even while progressive-enhancement CSS is loading.
        this.canvas.style.display = 'block';
        await (document.fonts?.ready || Promise.resolve());
        await this.waitForLayout();
        this.rebuild();
        if (!this.particles.length) throw new Error('Particle typography could not be sampled.');
        window.addEventListener('pointermove', this.boundPointerMove, { passive: true });
        document.documentElement.addEventListener('mouseleave', this.boundPointerLeave);
        window.addEventListener('resize', this.boundResize, { passive: true });
        window.addEventListener('pageshow', this.boundPageShow);
        document.addEventListener('visibilitychange', this.boundVisibility);
        this.observer = new IntersectionObserver(([entry]) => {
            this.visible = entry.isIntersecting;
            if (this.visible) this.start(); else this.stop();
        }, { rootMargin: '20% 0px', threshold: 0 });
        this.observer.observe(this.canvas);
        // Observe the rendered box directly so a late stylesheet/layout pass cannot
        // leave the canvas at its temporary 1x1 fallback buffer.
        if ('ResizeObserver' in window) {
            this.resizeObserver = new ResizeObserver(() => this.rebuild());
            this.resizeObserver.observe(this.canvas);
        }
        this.recoveryTimer = window.setInterval(() => this.recoverIfNeeded(), 1000);
        this.start();
    }

    async waitForLayout() {
        for (let attempt = 0; attempt < 20; attempt += 1) {
            const { width, height } = this.canvas.getBoundingClientRect();
            if (width > 32 && height > 32) return;
            await new Promise((resolve) => requestAnimationFrame(resolve));
        }

        throw new Error('Particle canvas has no renderable layout.');
    }

    rebuild() {
        const rect = this.canvas.getBoundingClientRect();
        const width = Math.max(1, Math.round(rect.width));
        const height = Math.max(1, Math.round(rect.height));
        const dpr = Math.min(window.devicePixelRatio || 1, width < 768 ? 1.25 : 1.5);
        const expectedBufferWidth = Math.round(width * dpr);
        const expectedBufferHeight = Math.round(height * dpr);
        const hasValidBuffer = this.canvas.width === expectedBufferWidth
            && this.canvas.height === expectedBufferHeight;
        if (width === this.lastWidth && height === this.lastHeight && this.particles.length && hasValidBuffer) return;
        this.lastWidth = width;
        this.lastHeight = height;
        this.dpr = dpr;
        this.canvas.width = expectedBufferWidth;
        this.canvas.height = expectedBufferHeight;
        this.context.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
        this.sampleWord(width, height);
    }

    sampleWord(width, height) {
        const offscreen = document.createElement('canvas');
        offscreen.width = width;
        offscreen.height = height;
        const context = offscreen.getContext('2d', { willReadFrequently: true });
        const isMobile = width < 768;
        const fontSize = Math.min(width * (isMobile ? .205 : .19), height * .35);
        context.clearRect(0, 0, width, height);
        context.fillStyle = '#ffffff';
        context.font = `500 ${fontSize}px "Playfair Display", Georgia, serif`;
        context.textAlign = 'center';
        context.textBaseline = 'middle';
        context.fillText('AANAYA', width / 2, height * (isMobile ? .47 : .5));

        const imageData = context.getImageData(0, 0, width, height).data;
        const targetCount = isMobile ? clamp(Math.round(width * 2.05), 500, 950) : clamp(Math.round(width * 1.8), 1600, 3200);
        const estimatedArea = Math.max(1, fontSize * width * .55);
        const step = Math.max(2, Math.round(Math.sqrt(estimatedArea / targetCount)));
        const sampled = [];

        for (let y = 0; y < height; y += step) {
            for (let x = 0; x < width; x += step) {
                if (imageData[((y * width) + x) * 4 + 3] < 100) continue;
                sampled.push({ x, y });
            }
        }

        const stride = Math.max(1, Math.ceil(sampled.length / targetCount));
        this.particles.length = 0;
        for (let index = 0; index < sampled.length; index += stride) {
            const point = sampled[index];
            const depth = .28 + Math.random() * .72;
            const angle = Math.random() * Math.PI * 2;
            const distance = 22 + Math.random() * Math.min(width, height) * .13;
            this.particles.push({
                x: point.x + Math.cos(angle) * distance,
                y: point.y + Math.sin(angle) * distance,
                homeX: point.x,
                homeY: point.y,
                startX: point.x + Math.cos(angle) * distance,
                startY: point.y + Math.sin(angle) * distance,
                vx: 0,
                vy: 0,
                size: .65 + depth * 1.55,
                depth,
                alpha: .42 + depth * .52,
                phase: Math.random() * Math.PI * 2,
            });
        }
    }

    handlePointerMove(event) {
        const rect = this.canvas.getBoundingClientRect();
        this.pointer.x = event.clientX - rect.left;
        this.pointer.y = event.clientY - rect.top;
        this.pointer.active = event.clientY >= rect.top && event.clientY <= rect.bottom;
    }

    handleResize() {
        window.clearTimeout(this.resizeTimer);
        this.resizeTimer = window.setTimeout(() => this.rebuild(), 180);
    }

    handleVisibility() {
        if (document.hidden) {
            this.stop();
            return;
        }

        this.rebuild();
        if (this.visible) this.start();
    }

    handlePageShow() {
        this.rebuild();
        if (this.visible) this.start();
    }

    recoverIfNeeded() {
        if (document.hidden || !this.visible) return;

        const rect = this.canvas.getBoundingClientRect();
        const hasInvalidBuffer = this.canvas.width <= 1 || this.canvas.height <= 1;
        const hasStoppedRendering = this.lastRenderAt > 0 && Date.now() - this.lastRenderAt > 1800;

        if (!this.particles.length || hasInvalidBuffer) this.rebuild();
        if (!this.running || hasStoppedRendering) {
            this.stop();
            this.start();
        }

        if (rect.width > 32 && rect.height > 32 && (this.canvas.width <= 1 || this.canvas.height <= 1)) {
            this.lastWidth = 0;
            this.lastHeight = 0;
            this.rebuild();
        }
    }

    setFormationProgress(progress) { this.formationProgress = clamp(progress, 0, 1); }
    setScrollProgress(progress) { this.scrollProgress = clamp(progress, 0, 1); }

    start() {
        if (this.running || document.hidden || !this.visible) return;
        this.running = true;
        this.frame = requestAnimationFrame(this.boundRender);
    }

    stop() {
        this.running = false;
        if (this.frame !== null) cancelAnimationFrame(this.frame);
        this.frame = null;
    }

    render(time) {
        if (!this.running) return;
        this.lastRenderAt = Date.now();
        const width = this.lastWidth;
        const height = this.lastHeight;
        const context = this.context;
        const pointerRadius = clamp(width * .09, 86, 150);
        const dispersion = Math.max(0, (this.scrollProgress - .32) / .68);
        context.clearRect(0, 0, width, height);

        for (let index = 0; index < this.particles.length; index += 1) {
            const particle = this.particles[index];
            const outwardX = (particle.homeX - width / 2) * dispersion * (1.7 + particle.depth * 1.6);
            const outwardY = (particle.homeY - height / 2) * dispersion * (1.2 + particle.depth);
            const homeX = particle.startX + (particle.homeX - particle.startX) * this.formationProgress + outwardX;
            const homeY = particle.startY + (particle.homeY - particle.startY) * this.formationProgress + outwardY;
            const breathe = Math.sin(time * .00075 + particle.phase) * (.22 + particle.depth * .35);
            let forceX = (homeX - particle.x) * .055;
            let forceY = (homeY - particle.y) * .055 + breathe * .025;

            if (this.pointer.active && this.scrollProgress < .5) {
                const dx = particle.x - this.pointer.x;
                const dy = particle.y - this.pointer.y;
                const distanceSquared = dx * dx + dy * dy;
                if (distanceSquared > 0 && distanceSquared < pointerRadius * pointerRadius) {
                    const distance = Math.sqrt(distanceSquared);
                    const strength = (1 - distance / pointerRadius) * (1.1 + particle.depth * 1.2);
                    forceX += (dx / distance) * strength;
                    forceY += (dy / distance) * strength;
                }
            }

            particle.vx = (particle.vx + forceX) * .86;
            particle.vy = (particle.vy + forceY) * .86;
            particle.x += particle.vx;
            particle.y += particle.vy;

            const alpha = particle.alpha * (1 - dispersion * .82);
            context.fillStyle = index % 17 === 0
                ? `rgba(244,198,244,${alpha})`
                : index % 29 === 0
                    ? `rgba(245,67,81,${alpha})`
                    : `rgba(252,248,237,${alpha})`;
            context.beginPath();
            context.arc(particle.x, particle.y, particle.size * (1 + dispersion * .3), 0, Math.PI * 2);
            context.fill();
        }

        this.frame = requestAnimationFrame(this.boundRender);
    }

    destroy() {
        this.stop();
        this.observer?.disconnect();
        this.resizeObserver?.disconnect();
        window.clearTimeout(this.resizeTimer);
        window.clearInterval(this.recoveryTimer);
        window.removeEventListener('pointermove', this.boundPointerMove);
        document.documentElement.removeEventListener('mouseleave', this.boundPointerLeave);
        window.removeEventListener('resize', this.boundResize);
        window.removeEventListener('pageshow', this.boundPageShow);
        document.removeEventListener('visibilitychange', this.boundVisibility);
    }
}
