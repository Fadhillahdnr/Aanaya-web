import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export class DashboardExperience {
    constructor(root) {
        this.root = root;
        this.cleanupCallbacks = [];
        this.matchMedia = gsap.matchMedia();
    }

    init() {
        this.root.classList.add('dashboard-experience--enhanced');
        this.initSpotifyPlayers();
        this.initChapterIndicator();

        this.matchMedia.add({
            desktop: '(min-width: 1025px) and (prefers-reduced-motion: no-preference)',
            mobile: '(max-width: 1024px) and (prefers-reduced-motion: no-preference)',
            reduced: '(prefers-reduced-motion: reduce)',
        }, (context) => {
            if (context.conditions.reduced) return;
            this.initHero(context.conditions.desktop);
            this.initSectionReveals();
            if (context.conditions.desktop) {
                this.initManifesto();
                this.initExploreMotion();
                this.initMemberParallax();
                this.initMusicMotion();
                this.initVisualParallax();
                this.initSignatureMotion();
                this.initEditorialMasks();
                this.initPointerExperience();
            }
        });

        const refresh = () => ScrollTrigger.refresh();
        window.addEventListener('load', refresh, { once: true });
        document.fonts?.ready.then(refresh).catch(() => {});
        this.cleanupCallbacks.push(() => window.removeEventListener('load', refresh));

        this.onPageHide = () => this.destroy();
        window.addEventListener('pagehide', this.onPageHide, { once: true });
        this.cleanupCallbacks.push(() => window.removeEventListener('pagehide', this.onPageHide));
    }

    initChapterIndicator() {
        const indicator = this.root.querySelector('[data-dashboard-chapter-indicator]');
        const chapters = [...this.root.querySelectorAll('[data-dashboard-chapter]')];
        const number = indicator?.querySelector('[data-dashboard-chapter-number]');
        const name = indicator?.querySelector('[data-dashboard-chapter-name]');
        const progress = indicator?.querySelector('[data-dashboard-chapter-progress]');
        if (!indicator || !chapters.length || !number || !name || !progress) return;

        const updateIndicator = () => {
            const marker = window.innerHeight * .48;
            const activeIndex = chapters.reduce((currentIndex, chapter, index) => (
                chapter.getBoundingClientRect().top <= marker ? index : currentIndex
            ), 0);
            const activeChapter = chapters[activeIndex];
            const chapterRect = activeChapter.getBoundingClientRect();
            const chapterProgress = Math.min(1, Math.max(0, (marker - chapterRect.top) / Math.max(chapterRect.height, 1)));

            number.textContent = activeChapter.dataset.dashboardChapterNumber;
            name.textContent = activeChapter.dataset.dashboardChapter;
            progress.style.transform = `scaleX(${chapterProgress})`;
        };

        let frame = null;
        const onScroll = () => {
            if (frame) return;
            frame = window.requestAnimationFrame(() => {
                updateIndicator();
                frame = null;
            });
        };

        updateIndicator();
        window.addEventListener('scroll', onScroll, { passive: true });
        this.cleanupCallbacks.push(() => {
            window.removeEventListener('scroll', onScroll);
            if (frame) window.cancelAnimationFrame(frame);
        });
    }

    initHero(isDesktop) {
        const hero = this.root.querySelector('[data-dashboard-hero]');
        if (!hero) return;

        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .from(hero.querySelector('.dashboard-hero__title'), { opacity: 0, y: 55, duration: 1.2 })
            .from(hero.querySelector('.dashboard-hero__portrait'), { opacity: 0, y: 45, scale: .96, duration: 1 }, '-=.8')
            .from(hero.querySelectorAll('[data-hero-intro]'), { opacity: 0, y: 16, stagger: .1, duration: .65 }, '-=.55');

        if (!isDesktop) return;
        gsap.timeline({ scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: 1.15 } })
            .to(hero.querySelector('[data-dashboard-depth="background"]'), { yPercent: 9, ease: 'none' }, 0)
            .to(hero.querySelector('[data-dashboard-depth="title"]'), { yPercent: -14, ease: 'none' }, 0)
            .to(hero.querySelector('[data-dashboard-depth="portrait"]'), { xPercent: 6, yPercent: -12, rotate: 2.5, scale: 1.04, ease: 'none' }, 0)
            .to(hero.querySelector('.dashboard-hero__copy'), { yPercent: -16, opacity: .15, ease: 'none' }, .15)
            .to(hero.querySelector('.dashboard-hero__scroll'), { opacity: 0, ease: 'none' }, 0);
    }

    initManifesto() {
        const section = this.root.querySelector('[data-dashboard-manifesto]');
        if (!section) return;
        gsap.timeline({ scrollTrigger: { trigger: section, start: 'top top', end: 'bottom bottom', scrub: 1.2 } })
            .fromTo(section.querySelector('[data-manifesto-line="left"]'), { xPercent: -11 }, { xPercent: 0, ease: 'none' }, 0)
            .fromTo(section.querySelector('[data-manifesto-line="right"]'), { xPercent: 10 }, { xPercent: 0, ease: 'none' }, 0)
            .fromTo(section.querySelector('[data-manifesto-line="scale"]'), { scale: .82, transformOrigin: 'left center' }, { scale: 1, ease: 'none' }, 0)
            .from(section.querySelector('.dashboard-manifesto__support'), { opacity: 0, y: 16, ease: 'none' }, .55);
    }

    initSectionReveals() {
        this.root.querySelectorAll('.dashboard-section-heading, .dashboard-music__heading').forEach((heading) => {
            gsap.from(heading.children, {
                opacity: 0, y: 18, stagger: .08, duration: .65, ease: 'power2.out',
                scrollTrigger: { trigger: heading, start: 'top 84%', once: true },
            });
        });
    }

    initExploreMotion() {
        const section = this.root.querySelector('[data-dashboard-explore]');
        const items = section?.querySelectorAll('[data-explore-item]');
        if (!section || !items?.length) return;
        const timeline = gsap.timeline({ scrollTrigger: { trigger: section, start: 'top 75%', end: 'bottom 85%', scrub: 1 } });
        items.forEach((item, index) => timeline.fromTo(item, { xPercent: index % 2 ? 3 : -4 }, { xPercent: 0, ease: 'none' }, 0));
    }

    initMemberParallax() {
        const section = this.root.querySelector('[data-dashboard-members]');
        section?.querySelectorAll('[data-member-depth]').forEach((member) => {
            const distance = Number(member.dataset.memberDepth) || 12;
            gsap.fromTo(member, { yPercent: distance / 2 }, {
                yPercent: -distance / 2, ease: 'none',
                scrollTrigger: { trigger: member, start: 'top bottom', end: 'bottom top', scrub: 1.25 },
            });
        });
    }

    initMusicMotion() {
        this.root.querySelectorAll('[data-release]').forEach((release, index) => {
            gsap.timeline({ scrollTrigger: { trigger: release, start: 'top bottom', end: 'bottom top', scrub: 1.2 } })
                .fromTo(release.querySelector('.dashboard-release__visual img'), { scale: 1.1 }, { scale: 1.02, ease: 'none' }, 0)
                .fromTo(release.querySelector('.dashboard-release__content'), { xPercent: index ? -6 : 6 }, { xPercent: 0, ease: 'none' }, 0);
        });
    }

    initVisualParallax() {
        const section = this.root.querySelector('[data-dashboard-visuals]');
        section?.querySelectorAll('[data-visual-depth]').forEach((visual) => {
            const distance = Number(visual.dataset.visualDepth) || 12;
            gsap.timeline({ scrollTrigger: { trigger: visual, start: 'top 95%', end: 'bottom 10%', scrub: 1.25 } })
                .fromTo(visual, { yPercent: distance / 2, rotate: distance % 2 ? -1.5 : 1.5 }, { yPercent: -distance / 2, rotate: 0, ease: 'none', duration: 1 }, 0)
                .fromTo(visual.querySelector('div'), { clipPath: 'inset(0 0 100% 0)' }, { clipPath: 'inset(0 0 0% 0)', ease: 'none', duration: .28 }, 0);
        });
    }

    initSignatureMotion() {
        const signature = this.root.querySelector('[data-dashboard-signature]');
        if (!signature) return;
        gsap.timeline({ scrollTrigger: { trigger: signature, start: 'top bottom', end: 'bottom top', scrub: 1.3 } })
            .fromTo(signature.querySelector('img'), { scale: 1.12, yPercent: -4 }, { scale: 1.02, yPercent: 4, ease: 'none' }, 0)
            .fromTo(signature.querySelector('div'), { yPercent: 10 }, { yPercent: -6, ease: 'none' }, 0);
    }

    initEditorialMasks() {
        this.root.querySelectorAll('.dashboard-member figure, .dashboard-release__visual').forEach((visual) => {
            gsap.fromTo(visual, {
                clipPath: 'inset(0 0 100% 0)',
            }, {
                clipPath: 'inset(0 0 0% 0)',
                duration: .85,
                ease: 'power3.inOut',
                scrollTrigger: { trigger: visual, start: 'top 86%', once: true },
            });
        });
    }

    initPointerExperience() {
        const aura = this.root.querySelector('[data-explore-aura]');
        const auraGlow = aura?.querySelector('.dashboard-explore__aura-glow');
        const auraOrbit = aura?.querySelector('.dashboard-explore__aura-orbit');
        const auraLabel = aura?.querySelector('[data-explore-aura-label]');
        const cursor = this.root.querySelector('[data-dashboard-cursor]');
        const cursorLabel = cursor?.querySelector('span');
        if (!aura || !auraGlow || !auraOrbit || !auraLabel || !cursor || !cursorLabel) return;

        const moveAuraX = gsap.quickTo(aura, 'x', { duration: .62, ease: 'power3.out' });
        const moveAuraY = gsap.quickTo(aura, 'y', { duration: .62, ease: 'power3.out' });
        const moveCursorX = gsap.quickTo(cursor, 'x', { duration: .2, ease: 'power3.out' });
        const moveCursorY = gsap.quickTo(cursor, 'y', { duration: .2, ease: 'power3.out' });
        const auraMotion = gsap.timeline({ paused: true, repeat: -1, yoyo: true })
            .to(auraGlow, { rotate: 9, scale: 1.045, duration: 2.8, ease: 'sine.inOut' }, 0)
            .to(auraOrbit, { rotate: 20, scaleY: .76, duration: 2.8, ease: 'sine.inOut' }, 0);

        const onPointerMove = (event) => {
            moveAuraX(event.clientX);
            moveAuraY(event.clientY);
            moveCursorX(event.clientX);
            moveCursorY(event.clientY);
            gsap.to(cursor, {
                opacity: event.clientX <= 8 || event.clientY <= 8 ? 0 : 1,
                duration: .2,
                overwrite: true,
            });
        };
        const onPointerLeave = () => {
            gsap.to([cursor, aura], { opacity: 0, duration: .2, overwrite: true });
            auraMotion.pause();
        };
        window.addEventListener('pointermove', onPointerMove, { passive: true });
        document.documentElement.addEventListener('mouseleave', onPointerLeave);

        this.root.querySelectorAll('[data-explore-item]').forEach((item) => {
            const onEnter = () => {
                aura.dataset.dreamTheme = item.dataset.dreamTheme;
                auraLabel.textContent = item.querySelector('strong')?.textContent || 'Explore';
                cursorLabel.textContent = 'Open';
                gsap.to(aura, { opacity: .92, scale: 1, duration: .42, ease: 'power3.out' });
                gsap.to(cursor, { scale: .82, duration: .25 });
                auraMotion.play();
            };
            const onLeave = () => {
                cursorLabel.textContent = 'Explore';
                gsap.to(aura, { opacity: 0, scale: .78, duration: .28 });
                gsap.to(cursor, { scale: 1, duration: .25 });
                auraMotion.pause();
            };
            item.addEventListener('pointerenter', onEnter);
            item.addEventListener('pointerleave', onLeave);
            this.cleanupCallbacks.push(() => {
                item.removeEventListener('pointerenter', onEnter);
                item.removeEventListener('pointerleave', onLeave);
            });
        });
        this.cleanupCallbacks.push(() => {
            window.removeEventListener('pointermove', onPointerMove);
            document.documentElement.removeEventListener('mouseleave', onPointerLeave);
            auraMotion.kill();
        });
    }

    initSpotifyPlayers() {
        this.root.querySelectorAll('[data-spotify-load]').forEach((button) => {
            const onClick = () => {
                const mount = button.parentElement.querySelector('[data-spotify-mount]');
                if (!mount || mount.querySelector('iframe')) return;
                const iframe = document.createElement('iframe');
                iframe.src = button.dataset.spotifySrc;
                iframe.title = `Spotify player for ${button.closest('[data-release]')?.querySelector('h3')?.textContent || 'Aanaya'}`;
                iframe.loading = 'lazy';
                iframe.allow = 'autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture';
                mount.append(iframe);
                button.setAttribute('aria-expanded', 'true');
                button.textContent = 'Spotify player loaded';
                ScrollTrigger.refresh();
            };
            button.addEventListener('click', onClick);
            this.cleanupCallbacks.push(() => button.removeEventListener('click', onClick));
        });
    }

    destroy() {
        this.matchMedia.revert();
        this.cleanupCallbacks.splice(0).forEach((cleanup) => cleanup());
        this.root.classList.remove('dashboard-experience--enhanced');
    }
}
