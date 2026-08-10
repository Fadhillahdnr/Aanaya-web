import { clamp } from './ArticleSceneConfig';

const range = (progress, start, end) => clamp((progress - start) / (end - start));

export class ArticleScroll {
    constructor(root, gsap, ScrollTrigger, prefersReducedMotion) {
        this.root = root;
        this.gsap = gsap;
        this.ScrollTrigger = ScrollTrigger;
        this.prefersReducedMotion = prefersReducedMotion;
        this.triggers = [];
    }

    init() {
        if (this.prefersReducedMotion) return;
        this.context = this.gsap.context(() => {
            this.animateHeroEntrance();
            this.animateChapters();
            this.trackReadingProgress();
        }, this.root);
    }

    updateScene(sceneId, state) {
        const scene = this.root.querySelector(`[data-scene="${sceneId}"]`);
        if (!scene) return;
        const { progress, videoProgress, isHolding } = state;
        scene.style.setProperty('--scene-progress', progress.toFixed(4));

        if (sceneId === 'reading') {
            this.gsap.set(scene.querySelector('[data-cinematic-media]'), { scale: 1 + videoProgress * .07, filter: `saturate(${1 - range(progress, .82, 1) * .55}) blur(${range(progress, .9, 1) * 3}px)` });
            this.gsap.set(scene.querySelector('.article-cinematic__content'), { yPercent: -12 * range(progress, .48, .86), opacity: 1 - range(progress, .64, .9) });
            this.gsap.set(scene.querySelector('[data-scene-transition]'), { opacity: range(progress, .82, 1) });
        }

        if (sceneId === 'approach-book') {
            this.gsap.set(scene.querySelector('[data-scene-transition="entry"]'), { opacity: 1 - range(progress, 0, .14) });
            this.gsap.set(scene.querySelector('[data-scene-transition="exit"]'), { opacity: range(progress, .84, 1) });
            this.gsap.set(scene.querySelector('[data-scene-copy]'), { opacity: range(progress, .18, .34) * (1 - range(progress, .7, .86)), y: 24 * (1 - range(progress, .18, .34)) });
        }

        if (sceneId === 'enter-book') {
            const paperProgress = range(progress, .72, 1);
            this.gsap.set(scene.querySelector('[data-portal-copy]'), { opacity: range(progress, .08, .25) * (1 - range(progress, .54, .72)), y: 30 * (1 - range(progress, .08, .25)) });
            this.gsap.set(scene.querySelector('[data-portal-paper]'), { opacity: paperProgress, scale: .2 + paperProgress * 1.45, borderRadius: `${50 * (1 - paperProgress)}%` });
        }

        if (sceneId === 'paper-plane') {
            this.gsap.set(scene.querySelector('[data-interlude-copy]'), { opacity: range(progress, .18, .34) * (1 - range(progress, .7, .86)), y: 28 * (1 - range(progress, .18, .34)) });
            const veil = scene.querySelector('.article-interlude__veil');
            if (veil) veil.style.opacity = String(Math.max(1 - range(progress, 0, .14), range(progress, .84, 1)));
        }

        if (sceneId === 'ending') {
            const reveal = isHolding ? range(progress, .82, 1) : 0;
            const content = scene.querySelector('[data-ending-content]');
            this.gsap.set(content, { opacity: reveal, y: 36 * (1 - reveal) });
        }
    }

    animateHeroEntrance() {
        const hero = this.root.querySelector('.article-cinematic');
        const title = hero?.querySelector('[data-hero-title]');
        if (!title) return;
        this.gsap.from(title, { opacity: 0, y: 52, filter: 'blur(9px)', duration: 1.2, ease: 'power4.out' });
        this.gsap.from(hero.querySelectorAll('[data-hero-reveal]'), { opacity: 0, y: 18, duration: .8, stagger: .1, delay: .2, ease: 'power3.out' });
    }

    animateChapters() {
        this.root.querySelectorAll('[data-story-reveal]').forEach((element) => this.gsap.from(element, {
            opacity: 0, y: 18, duration: .75, ease: 'power3.out',
            scrollTrigger: { trigger: element, start: 'top 84%', once: true },
        }));
        this.root.querySelectorAll('[data-story-image] img').forEach((image) => this.gsap.to(image, {
            yPercent: -4, scale: 1.06, ease: 'none',
            scrollTrigger: { trigger: image, start: 'top bottom', end: 'bottom top', scrub: 1.4 },
        }));
    }

    trackReadingProgress() {
        const story = this.root.querySelector('[data-article-story]');
        const progress = this.root.querySelector('[data-article-progress]');
        const fill = this.root.querySelector('[data-progress-fill]');
        const value = this.root.querySelector('[data-progress-value]');
        if (!story || !progress || !fill || !value) return;
        this.triggers.push(this.ScrollTrigger.create({
            trigger: story,
            start: 'top top',
            end: 'bottom bottom',
            onToggle: ({ isActive }) => progress.classList.toggle('is-active', isActive),
            onUpdate: ({ progress: readingProgress }) => {
                fill.style.transform = `scaleY(${readingProgress})`;
                value.value = `${Math.round(readingProgress * 100)}%`;
            },
        }));
    }

    destroy() {
        this.triggers.splice(0).forEach((trigger) => trigger.kill());
        this.context?.revert();
    }
}
