import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function initFooterExperience(root) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const context = gsap.context(() => {
        gsap.from(root.querySelectorAll('[data-footer-reveal]'), {
            opacity: 0,
            y: 14,
            duration: .4,
            stagger: .06,
            ease: 'power1.out',
            scrollTrigger: { trigger: root, start: 'top 78%', once: true },
        });

        gsap.fromTo(root.querySelector('[data-footer-wordmark]'),
            { xPercent: -2, scale: .97 },
            {
                xPercent: 1.5,
                scale: 1,
                ease: 'none',
                scrollTrigger: { trigger: root, start: 'top bottom', end: 'bottom bottom', scrub: .7 },
            });
    }, root);

    const destroy = (event) => {
        if (!event.persisted) context.revert();
    };
    window.addEventListener('pagehide', destroy, { once: true });
}
