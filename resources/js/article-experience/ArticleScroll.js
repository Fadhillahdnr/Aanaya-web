export class ArticleScroll {
    constructor(root, gsap, ScrollTrigger, prefersReducedMotion) {
        this.root = root;
        this.gsap = gsap;
        this.ScrollTrigger = ScrollTrigger;
        this.prefersReducedMotion = prefersReducedMotion;
        this.context = null;
        this.triggers = [];
    }

    init() {
        if (this.prefersReducedMotion) {
            return;
        }

        this.context = this.gsap.context(() => {
            this.animateHero();
            this.animatePortal();
            this.animateChapters();
            this.animateInterludes();
            this.animateEnding();
            this.trackReadingProgress();
        }, this.root);
    }

    animateHero() {
        const hero = this.root.querySelector('.article-cinematic');
        const title = hero?.querySelector('[data-hero-title]');
        const supporting = hero?.querySelectorAll('[data-hero-reveal]');
        const media = hero?.querySelector('[data-cinematic-media]');

        if (!hero || !title || !media) return;

        this.gsap.from(title, { opacity: 0, y: 70, rotateX: -16, filter: 'blur(12px)', duration: 1.35, ease: 'power4.out' });
        this.gsap.from(supporting, { opacity: 0, y: 24, duration: .9, stagger: .12, delay: .25, ease: 'power3.out' });

        this.gsap.timeline({
            scrollTrigger: {
                trigger: hero,
                start: 'top top',
                end: 'bottom bottom',
                scrub: 1.25,
            },
        })
            .to(media, { scale: 1.15, filter: 'saturate(.8) brightness(.72)', ease: 'none' }, 0)
            .to('.article-cinematic__content', { yPercent: -18, opacity: .12, ease: 'none' }, .2);
    }

    animatePortal() {
        const portal = this.root.querySelector('.article-portal');
        const paper = portal?.querySelector('[data-portal-paper]');
        const copy = portal?.querySelector('[data-portal-copy]');

        if (!portal || !paper || !copy) return;

        this.gsap.timeline({
            scrollTrigger: {
                trigger: portal,
                start: 'top top',
                end: 'bottom bottom',
                scrub: 1.3,
            },
        })
            .fromTo(copy, { opacity: 0, y: 50 }, { opacity: 1, y: 0, duration: .22, ease: 'none' })
            .to(copy, { scale: .86, opacity: 0, duration: .24, ease: 'none' }, .55)
            .to(paper, { scale: 1.55, opacity: 1, borderRadius: '0%', filter: 'blur(0px)', duration: .45, ease: 'power2.in' }, .5);
    }

    animateChapters() {
        this.root.querySelectorAll('[data-story-reveal]').forEach((element) => {
            this.gsap.from(element, {
                opacity: 0,
                y: 32,
                duration: .9,
                ease: 'power3.out',
                scrollTrigger: { trigger: element, start: 'top 82%', once: true },
            });
        });

        this.root.querySelectorAll('[data-story-image]').forEach((figure, index) => {
            const mask = figure.querySelector('.article-chapter__image-mask');
            const image = figure.querySelector('img');
            const light = figure.querySelector('.article-chapter__light');

            if (!mask || !image) return;

            this.gsap.from(mask, {
                clipPath: index % 2 === 0
                    ? 'inset(0 100% 0 0 round 48% 48% 1.5rem 1.5rem / 12% 12% 1.5rem 1.5rem)'
                    : 'inset(0 0 0 100% round 48% 48% 1.5rem 1.5rem / 12% 12% 1.5rem 1.5rem)',
                duration: 1.15,
                ease: 'power4.out',
                scrollTrigger: { trigger: figure, start: 'top 80%', once: true },
            });

            this.gsap.to(image, {
                yPercent: -7,
                scale: 1.09,
                ease: 'none',
                scrollTrigger: { trigger: figure, start: 'top bottom', end: 'bottom top', scrub: 1.1 },
            });

            if (light) {
                this.gsap.to(light, {
                    xPercent: 80,
                    ease: 'none',
                    scrollTrigger: { trigger: figure, start: 'top 85%', end: 'bottom 20%', scrub: 1.3 },
                });
            }
        });
    }

    animateInterludes() {
        this.root.querySelectorAll('.article-interlude').forEach((interlude) => {
            const copy = interlude.querySelector('[data-interlude-copy]');
            if (!copy) return;

            this.gsap.fromTo(copy, { opacity: 0, y: 50 }, {
                opacity: 1,
                y: -20,
                ease: 'none',
                scrollTrigger: { trigger: interlude, start: 'top 70%', end: 'bottom 45%', scrub: 1.25 },
            });
        });
    }

    animateEnding() {
        const ending = this.root.querySelector('.article-ending');
        const content = ending?.querySelector('[data-ending-content]');
        if (!ending || !content) return;

        this.gsap.from(content.children, {
            opacity: 0,
            y: 36,
            duration: .9,
            stagger: .11,
            ease: 'power3.out',
            scrollTrigger: { trigger: ending, start: 'top 62%', once: true },
        });
    }

    trackReadingProgress() {
        const fill = this.root.querySelector('[data-progress-fill]');
        const value = this.root.querySelector('[data-progress-value]');
        if (!fill || !value) return;

        const trigger = this.ScrollTrigger.create({
            trigger: this.root,
            start: 'top top',
            end: 'bottom bottom',
            onUpdate: ({ progress }) => {
                fill.style.transform = `scaleY(${progress})`;
                value.value = `${Math.round(progress * 100)}%`;
            },
        });

        this.triggers.push(trigger);
    }

    destroy() {
        this.triggers.splice(0).forEach((trigger) => trigger.kill());
        this.context?.revert();
    }
}
