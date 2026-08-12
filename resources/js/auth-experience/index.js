import { gsap } from 'gsap';

export function initAuthExperience(root) {
    const cleanups = [];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    root.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const passwordInput = root.querySelector(`#${CSS.escape(toggle.dataset.passwordTarget)}`);
        if (!passwordInput) return;

        const togglePassword = () => {
            const shouldShowPassword = passwordInput.type === 'password';
            passwordInput.type = shouldShowPassword ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(shouldShowPassword));
            toggle.setAttribute('aria-label', shouldShowPassword ? 'Hide password' : 'Show password');
        };

        toggle.addEventListener('click', togglePassword);
        cleanups.push(() => toggle.removeEventListener('click', togglePassword));
    });

    root.querySelectorAll('[data-auth-form-element]').forEach((form) => {
        const submitButton = form.querySelector('[data-auth-submit]');
        const submitLabel = submitButton?.querySelector('[data-submit-label]');
        const originalLabel = submitLabel?.textContent;
        const handleSubmit = () => {
            if (!submitButton || !form.checkValidity()) return;
            submitButton.disabled = true;
            submitButton.classList.add('is-loading');
            submitButton.setAttribute('aria-busy', 'true');
            if (submitLabel) submitLabel.textContent = 'Please wait';
        };

        form.addEventListener('submit', handleSubmit);
        cleanups.push(() => {
            form.removeEventListener('submit', handleSubmit);
            if (!submitButton) return;
            submitButton.disabled = false;
            submitButton.classList.remove('is-loading');
            submitButton.removeAttribute('aria-busy');
            if (submitLabel && originalLabel) submitLabel.textContent = originalLabel;
        });
    });

    if (!reducedMotion) {
        gsap.from(root.querySelector('[data-auth-reveal]'), { opacity: 0, y: 12, duration: .55, ease: 'power1.out' });
        gsap.from(root.querySelector('[data-auth-form]'), { opacity: 0, y: 10, duration: .42, delay: .08, ease: 'power1.out' });
    }

    if (!reducedMotion && finePointer) {
        const layers = [...root.querySelectorAll('[data-auth-depth]')];
        const setters = layers.map((layer) => ({
            x: gsap.quickTo(layer, 'x', { duration: .55, ease: 'power2.out' }),
            y: gsap.quickTo(layer, 'y', { duration: .55, ease: 'power2.out' }),
            depth: Number(layer.dataset.authDepth || 0),
        }));
        const handlePointerMove = (event) => {
            const normalizedX = event.clientX / window.innerWidth * 2 - 1;
            const normalizedY = event.clientY / window.innerHeight * 2 - 1;
            setters.forEach((setter) => {
                setter.x(normalizedX * 10 * setter.depth);
                setter.y(normalizedY * 7 * setter.depth);
            });
        };

        window.addEventListener('pointermove', handlePointerMove, { passive: true });
        cleanups.push(() => window.removeEventListener('pointermove', handlePointerMove));
    }

    window.addEventListener('pagehide', () => cleanups.splice(0).forEach((cleanup) => cleanup()), { once: true });
}
