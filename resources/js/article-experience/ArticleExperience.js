import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { ArticleAtmosphere } from './ArticleAtmosphere';
import { ArticleAudio } from './ArticleAudio';
import { ArticleScroll } from './ArticleScroll';
import { ArticleVideo } from './ArticleVideo';

gsap.registerPlugin(ScrollTrigger);

export class ArticleExperience {
    constructor(root) {
        this.root = root;
        this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.hasFinePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        this.modules = [];
        this.cleanupCallbacks = [];
    }

    init() {
        this.root.classList.add('article-experience--enhanced');

        const scroll = new ArticleScroll(this.root, gsap, ScrollTrigger, this.prefersReducedMotion);
        scroll.init();
        this.modules.push(scroll);

        const audio = new ArticleAudio(this.root, this.prefersReducedMotion);
        audio.init();
        this.modules.push(audio);

        let atmosphere = null;

        if (this.canUseAtmosphere()) {
            try {
                atmosphere = new ArticleAtmosphere(this.root.querySelector('[data-article-canvas]'));
                atmosphere.init();
                this.modules.push(atmosphere);
            } catch (error) {
                this.root.classList.add('article-experience--webgl-fallback');
                console.warn('WebGL atmosphere unavailable; using the cinematic CSS fallback.', error);
            }
        }

        const videos = new ArticleVideo(
            this.root,
            ScrollTrigger,
            this.prefersReducedMotion,
            (sceneId, state) => {
                scroll.updateScene(sceneId, state);
                audio.setTargetTime({ time: state.globalAudioTime, moving: state.moving });
                atmosphere?.updateScene?.(sceneId, state);
            },
        );
        videos.init();
        this.modules.push(videos);

        if (this.hasFinePointer && !this.prefersReducedMotion) {
            this.initCursor();
        }

        const refresh = () => ScrollTrigger.refresh();
        window.addEventListener('load', refresh, { once: true });
        document.fonts?.ready.then(refresh).catch(() => {});
        this.cleanupCallbacks.push(() => window.removeEventListener('load', refresh));

        const destroy = () => this.destroy();
        window.addEventListener('pagehide', destroy, { once: true });
        this.cleanupCallbacks.push(() => window.removeEventListener('pagehide', destroy));
    }

    canUseAtmosphere() {
        if (this.prefersReducedMotion || window.innerWidth < 768) {
            return false;
        }

        const canvas = document.createElement('canvas');

        return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    }

    initCursor() {
        const cursor = this.root.querySelector('[data-article-cursor]');
        const cursorLabel = cursor?.querySelector('[data-cursor-label]');

        if (!cursor || !cursorLabel) {
            return;
        }

        const moveX = gsap.quickTo(cursor, 'x', { duration: 0.28, ease: 'power3.out' });
        const moveY = gsap.quickTo(cursor, 'y', { duration: 0.28, ease: 'power3.out' });
        const onPointerMove = (event) => {
            moveX(event.clientX);
            moveY(event.clientY);
            gsap.to(cursor, { autoAlpha: 1, scale: 1, duration: 0.2, overwrite: true });
        };
        const onPointerLeave = () => gsap.to(cursor, { autoAlpha: 0, scale: 0.55, duration: 0.2 });
        const interactiveElements = this.root.querySelectorAll('[data-cursor]');

        const cursorListeners = Array.from(interactiveElements, (element) => {
            const enter = () => {
                cursorLabel.textContent = element.dataset.cursor || 'Explore';
                gsap.to(cursor, { scale: 1.2, backgroundColor: 'rgba(109, 38, 58, .9)', duration: 0.25 });
            };
            const leave = () => {
                cursorLabel.textContent = 'Explore';
                gsap.to(cursor, { scale: 1, backgroundColor: 'rgba(109, 38, 58, .72)', duration: 0.25 });
            };

            element.addEventListener('pointerenter', enter);
            element.addEventListener('pointerleave', leave);

            return () => {
                element.removeEventListener('pointerenter', enter);
                element.removeEventListener('pointerleave', leave);
            };
        });

        window.addEventListener('pointermove', onPointerMove, { passive: true });
        document.documentElement.addEventListener('mouseleave', onPointerLeave);

        this.cleanupCallbacks.push(() => {
            window.removeEventListener('pointermove', onPointerMove);
            document.documentElement.removeEventListener('mouseleave', onPointerLeave);
            cursorListeners.forEach((removeListener) => removeListener());
        });
    }

    destroy() {
        this.modules.splice(0).reverse().forEach((module) => module.destroy?.());
        this.cleanupCallbacks.splice(0).forEach((cleanup) => cleanup());
        this.root.classList.remove('article-experience--enhanced');
    }
}
