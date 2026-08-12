import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export class AboutExperience {
    constructor(root) {
        this.root = root;
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        this.cleanups = [];
    }

    init() {
        this.initCharacterVideo();
        if (this.reducedMotion) return;

        this.context = gsap.context(() => {
            this.initOrbit();
            this.initReveals();
            this.initFeeling();
            this.initConstellation();
            this.initIdentity();
        }, this.root);

        if (this.finePointer) this.initPointerDepth();
        document.fonts?.ready.then(() => ScrollTrigger.refresh()).catch(() => {});

        const destroy = (event) => {
            if (!event.persisted) this.destroy();
        };
        window.addEventListener('pagehide', destroy, { once: true });
        this.cleanups.push(() => window.removeEventListener('pagehide', destroy));
    }

    initOrbit() {
        const orbit = this.root.querySelector('[data-about-orbit]');
        const orbitObjects = [...orbit.querySelectorAll('[data-orbit-object]')];
        const title = orbit.querySelector('.about-orbit__copy');

        this.orbitTrigger = ScrollTrigger.create({
            trigger: orbit,
            start: 'top top',
            end: 'bottom bottom',
            scrub: .8,
            onUpdate: ({ progress }) => {
                orbitObjects.forEach((object, index) => {
                    const depth = Number(object.dataset.depth || .5);
                    const travel = Number(object.dataset.travel || -30);
                    gsap.set(object, {
                        yPercent: progress * travel * (1 + depth),
                        xPercent: Math.sin(progress * Math.PI + index) * depth * 13,
                        rotateY: (progress - .5) * depth * 10,
                        rotateX: Math.sin(progress * Math.PI) * depth * 4,
                    });
                });
                gsap.set(title, { scale: 1 - progress * .08, opacity: 1 - Math.max(0, (progress - .72) / .25), yPercent: -8 * progress });
            },
        });
    }

    initReveals() {
        this.root.querySelectorAll('[data-about-reveal]').forEach((element) => {
            gsap.from(element, {
                opacity: 0,
                y: 18,
                duration: .55,
                ease: 'power2.out',
                scrollTrigger: { trigger: element, start: 'top 88%', once: true },
            });
        });
    }

    initFeeling() {
        this.root.querySelectorAll('[data-feeling-word]').forEach((word) => {
            gsap.fromTo(word, { xPercent: -Number(word.dataset.speed || 0) }, {
                xPercent: Number(word.dataset.speed || 0),
                ease: 'none',
                scrollTrigger: { trigger: word.closest('[data-about-feeling]'), start: 'top bottom', end: 'bottom top', scrub: .7 },
            });
        });
    }

    initConstellation() {
        this.root.querySelectorAll('[data-constellation-media]').forEach((media, index) => {
            gsap.from(media, {
                yPercent: 12 + index * 4,
                rotate: index % 2 ? 3 : -3,
                opacity: .45,
                ease: 'none',
                scrollTrigger: { trigger: media.closest('.about-constellation'), start: 'top bottom', end: 'bottom bottom', scrub: .8 },
            });
        });
    }

    initIdentity() {
        const identity = this.root.querySelector('[data-about-identity]');
        gsap.fromTo(identity.querySelector('.about-identity__type'), { xPercent: -54 }, {
            xPercent: -46,
            ease: 'none',
            scrollTrigger: { trigger: identity, start: 'top bottom', end: 'bottom top', scrub: .8 },
        });
        gsap.from(identity.querySelector('[data-about-character]'), {
            yPercent: 10,
            rotate: -2,
            ease: 'none',
            scrollTrigger: { trigger: identity, start: 'top bottom', end: 'center center', scrub: .7 },
        });
    }

    initPointerDepth() {
        const stage = this.root.querySelector('.about-orbit__stage');
        const objects = [...stage.querySelectorAll('[data-orbit-object]')];
        const setters = objects.map((object) => ({
            x: gsap.quickTo(object, 'x', { duration: .7, ease: 'power3.out' }),
            y: gsap.quickTo(object, 'y', { duration: .7, ease: 'power3.out' }),
            depth: Number(object.dataset.depth || .5),
        }));
        const move = (event) => {
            const x = event.clientX / window.innerWidth * 2 - 1;
            const y = event.clientY / window.innerHeight * 2 - 1;
            setters.forEach((setter) => {
                setter.x(x * 18 * setter.depth);
                setter.y(y * 12 * setter.depth);
            });
        };
        window.addEventListener('pointermove', move, { passive: true });
        this.cleanups.push(() => window.removeEventListener('pointermove', move));
    }

    initCharacterVideo() {
        const video = this.root.querySelector('[data-about-character-video]');
        if (!video) return;
        let sourcesLoaded = false;
        const observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) {
                if (!sourcesLoaded) {
                    video.querySelectorAll('source[data-src]').forEach((source) => { source.src = source.dataset.src; });
                    video.load();
                    sourcesLoaded = true;
                }
                video.play().catch(() => {});
            } else {
                video.pause();
            }
        }, { rootMargin: '180px 0px', threshold: .1 });
        observer.observe(video);
        this.cleanups.push(() => { observer.disconnect(); video.pause(); });
    }

    destroy() {
        this.orbitTrigger?.kill();
        this.context?.revert();
        this.cleanups.splice(0).forEach((cleanup) => cleanup());
    }
}
