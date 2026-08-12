import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { MUSIC_SCENES } from './MusicSceneConfig';
import { MusicVideoScrubber } from './MusicVideoScrubber';
import { MusicPlayer } from './MusicPlayer';

gsap.registerPlugin(ScrollTrigger);

export class MusicExperience {
    constructor(root) { this.root = root; this.reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches; this.saveData = navigator.connection?.saveData === true; this.finePointer = matchMedia('(hover:hover) and (pointer:fine)').matches; this.scrubbers = []; this.cleanups = []; }
    init() {
        const tracks = JSON.parse(this.root.querySelector('[data-music-tracks]')?.textContent || '[]'); this.player = new MusicPlayer(this.root, tracks); this.player.init();
        if (!this.reducedMotion && !this.saveData) { this.context = gsap.context(() => { this.initScenes(); this.initReveals(); this.initCovers(); }, this.root); }
        const destroy = (event) => { if (!event.persisted) this.destroy(); }; addEventListener('pagehide', destroy, { once: true }); this.cleanups.push(() => removeEventListener('pagehide', destroy));
        document.fonts?.ready.then(() => ScrollTrigger.refresh()).catch(() => {});
    }
    initScenes() {
        const observer = new IntersectionObserver((entries) => entries.forEach((entry) => { if (entry.isIntersecting) entry.target.__scrubber?.load(); }), { rootMargin: '130% 0px' });
        this.root.querySelectorAll('[data-music-scene]').forEach((scene) => {
            const config = MUSIC_SCENES[scene.dataset.musicScene]; const video = scene.querySelector('video'); const copy = scene.querySelector('.music-cinema__copy');
            const scrubber = new MusicVideoScrubber(video, config.cues); video.__scrubber = scrubber; this.scrubbers.push(scrubber); observer.observe(video);
            ScrollTrigger.create({ trigger: scene, start: 'top top', end: 'bottom bottom', scrub: .65, onUpdate: ({ progress }) => { scrubber.seek(progress); const exit = Math.max(0, (progress - .8) / .2); gsap.set(copy, { yPercent: -8 * progress, scale: 1 - progress * .045, opacity: 1 - exit }); scene.classList.toggle('is-holding', scene.dataset.musicScene === 'ending' && progress > .96); } });
        });
        this.cleanups.push(() => observer.disconnect());
    }
    initReveals() { this.root.querySelectorAll('[data-music-reveal]').forEach((element) => gsap.from(element, { opacity: 0, y: 22, duration: .65, ease: 'power2.out', scrollTrigger: { trigger: element, start: 'top 87%', once: true } })); }
    initCovers() {
        this.root.querySelectorAll('[data-cover-tilt]').forEach((cover, index) => gsap.fromTo(cover, { rotateY: index % 2 ? -5 : 5, rotateX: 3 }, { rotateY: 0, rotateX: 0, ease: 'none', scrollTrigger: { trigger: cover, start: 'top bottom', end: 'center center', scrub: .7 } }));
        if (!this.finePointer) return;
        const move = (event) => { const target = event.target.closest('[data-cover-tilt]'); if (!target) return; const box = target.getBoundingClientRect(); gsap.to(target, { rotateY: ((event.clientX - box.left) / box.width - .5) * 8, rotateX: -((event.clientY - box.top) / box.height - .5) * 6, duration: .35, overwrite: true }); };
        const leave = (event) => { const target = event.target.closest?.('[data-cover-tilt]'); if (target) gsap.to(target, { rotateX: 0, rotateY: 0, duration: .45 }); };
        this.root.addEventListener('pointermove', move, { passive: true }); this.root.addEventListener('pointerout', leave, { passive: true }); this.cleanups.push(() => { this.root.removeEventListener('pointermove', move); this.root.removeEventListener('pointerout', leave); });
    }
    destroy() { this.player?.destroy(); this.scrubbers.forEach((scrubber) => scrubber.destroy()); this.context?.revert(); ScrollTrigger.getAll().filter((trigger) => this.root.contains(trigger.trigger)).forEach((trigger) => trigger.kill()); this.cleanups.splice(0).forEach((cleanup) => cleanup()); }
}
