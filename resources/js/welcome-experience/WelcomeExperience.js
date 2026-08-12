import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ParticleTypography } from './ParticleTypography';

gsap.registerPlugin(ScrollTrigger);

export class WelcomeExperience {
    constructor(root) {
        this.root = root;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        this.cleanups = [];
    }

    async init() {
        if (this.reducedMotion) return;
        const canvas = this.root.querySelector('[data-particle-canvas]');
        this.particles = new ParticleTypography(canvas);
        await this.particles.init();
        this.root.classList.add('welcome-experience--enhanced');
        this.initIntro();
        this.initScroll();
        this.initPortals();
        if (this.finePointer) this.initPointerEffects();

        const refresh = () => ScrollTrigger.refresh();
        document.fonts?.ready.then(refresh).catch(() => {});
        window.addEventListener('load', refresh, { once: true });
        this.cleanups.push(() => window.removeEventListener('load', refresh));
        const destroy = (event) => {
            // Keep animation state intact when the page enters the back-forward cache.
            if (!event.persisted) this.destroy();
        };
        window.addEventListener('pagehide', destroy, { once: true });
        this.cleanups.push(() => window.removeEventListener('pagehide', destroy));
    }

    initIntro() {
        const state = { progress: 0 };
        this.introTimeline = gsap.timeline({ defaults: { ease: 'power2.out' } })
            .to(state, { progress: 1, duration: 1.8, onUpdate: () => this.particles.setFormationProgress(state.progress) })
            .from('[data-intro-copy]', { y: 14, opacity: 0, duration: .65, stagger: .1 }, .9);
    }

    initScroll() {
        const hero = this.root.querySelector('[data-welcome-hero]');
        const manifesto = this.root.querySelector('[data-welcome-manifesto]');
        const nav = this.root.querySelector('.welcome-nav');
        const parallaxLayers = this.root.querySelectorAll('[data-depth]');

        this.heroTrigger = ScrollTrigger.create({
            trigger: hero,
            start: 'top top',
            end: 'bottom bottom',
            scrub: .7,
            onUpdate: ({ progress }) => {
                this.particles.setScrollProgress(progress);
                gsap.set('.welcome-hero__content', { yPercent: -12 * progress, opacity: 1 - Math.max(0, (progress - .5) / .4) });
                for (let index = 0; index < parallaxLayers.length; index += 1) {
                    const layer = parallaxLayers[index];
                    gsap.set(layer, { yPercent: progress * -24 * Number(layer.dataset.depth || .1), scale: 1 + progress * Number(layer.dataset.depth || .1) * .18 });
                }
            },
        });

        this.manifestoTimeline = gsap.timeline({
            scrollTrigger: { trigger: manifesto, start: 'top 72%', end: 'bottom 70%', scrub: .7 },
        });
        this.manifestoTimeline.from('[data-manifesto-word]', { xPercent: (index) => index % 2 ? 12 : -8, opacity: .22, scale: .88, stagger: .08, ease: 'none' });

        this.navTrigger = ScrollTrigger.create({
            trigger: manifesto,
            start: 'top 76px',
            end: 'bottom 76px',
            toggleClass: { targets: nav, className: 'is-light' },
        });

        gsap.utils.toArray('.welcome-portals__header, .welcome-final > *:not(.welcome-final__halo)').forEach((element) => {
            const tween = gsap.from(element, { opacity: 0, y: 24, duration: .65, ease: 'power2.out', scrollTrigger: { trigger: element, start: 'top 88%', once: true } });
            this.cleanups.push(() => tween.kill());
        });
    }

    initPortals() {
        const preview = this.root.querySelector('[data-portal-preview]');
        const image = this.root.querySelector('[data-portal-preview-image]');
        const links = [...this.root.querySelectorAll('[data-portal-image]')];
        const listeners = links.map((link) => {
            const enter = () => {
                image.src = link.dataset.portalImage;
                gsap.to(preview, { autoAlpha: .42, scale: 1, rotate: 2, duration: .35, ease: 'power2.out', overwrite: true });
            };
            const leave = () => gsap.to(preview, { autoAlpha: 0, scale: .94, duration: .25, overwrite: true });
            link.addEventListener('pointerenter', enter);
            link.addEventListener('pointerleave', leave);
            link.addEventListener('focus', enter);
            link.addEventListener('blur', leave);
            return () => {
                link.removeEventListener('pointerenter', enter);
                link.removeEventListener('pointerleave', leave);
                link.removeEventListener('focus', enter);
                link.removeEventListener('blur', leave);
            };
        });
        this.cleanups.push(() => listeners.forEach((remove) => remove()));
    }

    initPointerEffects() {
        const cursor = this.root.querySelector('[data-welcome-cursor]');
        const label = cursor.querySelector('[data-cursor-label]');
        const moveX = gsap.quickTo(cursor, 'x', { duration: .22, ease: 'power3.out' });
        const moveY = gsap.quickTo(cursor, 'y', { duration: .22, ease: 'power3.out' });
        const layers = [...this.root.querySelectorAll('[data-depth]')];
        const onMove = (event) => {
            moveX(event.clientX);
            moveY(event.clientY);
            gsap.to(cursor, { autoAlpha: 1, scale: 1, duration: .18, overwrite: true });
            const normalizedX = event.clientX / window.innerWidth * 2 - 1;
            const normalizedY = event.clientY / window.innerHeight * 2 - 1;
            for (let index = 0; index < layers.length; index += 1) {
                const depth = Number(layers[index].dataset.depth || .1);
                gsap.to(layers[index], { x: normalizedX * 30 * depth, y: normalizedY * 22 * depth, duration: .7, ease: 'power3.out', overwrite: 'auto' });
            }
        };
        const onLeave = () => gsap.to(cursor, { autoAlpha: 0, scale: .7, duration: .18 });
        window.addEventListener('pointermove', onMove, { passive: true });
        document.documentElement.addEventListener('mouseleave', onLeave);

        const targets = [...this.root.querySelectorAll('[data-cursor]')];
        const targetCleanups = targets.map((target) => {
            const enter = () => { label.textContent = target.dataset.cursor; gsap.to(cursor, { scale: 1.22, duration: .2 }); };
            const leave = () => { label.textContent = 'Move'; gsap.to(cursor, { scale: 1, duration: .2 }); };
            target.addEventListener('pointerenter', enter);
            target.addEventListener('pointerleave', leave);
            return () => { target.removeEventListener('pointerenter', enter); target.removeEventListener('pointerleave', leave); };
        });

        const magnets = [...this.root.querySelectorAll('[data-magnetic]')];
        const magneticCleanups = magnets.map((element) => {
            const move = (event) => {
                const rect = element.getBoundingClientRect();
                gsap.to(element, { x: (event.clientX - rect.left - rect.width / 2) * .12, y: (event.clientY - rect.top - rect.height / 2) * .12, duration: .3, ease: 'power2.out' });
            };
            const leave = () => gsap.to(element, { x: 0, y: 0, duration: .45, ease: 'elastic.out(1, .45)' });
            element.addEventListener('pointermove', move);
            element.addEventListener('pointerleave', leave);
            return () => { element.removeEventListener('pointermove', move); element.removeEventListener('pointerleave', leave); };
        });

        this.cleanups.push(() => {
            window.removeEventListener('pointermove', onMove);
            document.documentElement.removeEventListener('mouseleave', onLeave);
            targetCleanups.forEach((remove) => remove());
            magneticCleanups.forEach((remove) => remove());
        });
    }

    destroy() {
        this.particles?.destroy();
        this.introTimeline?.kill();
        this.heroTrigger?.kill();
        this.manifestoTimeline?.kill();
        this.navTrigger?.kill();
        this.cleanups.splice(0).forEach((cleanup) => cleanup());
        this.root.classList.remove('welcome-experience--enhanced');
    }
}
