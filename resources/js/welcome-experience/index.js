import { WelcomeExperience } from './WelcomeExperience';

let activeExperience = null;

export async function initWelcomeExperience(root) {
    if (!root || activeExperience) return activeExperience;
    activeExperience = new WelcomeExperience(root);
    try {
        await activeExperience.init();
    } catch (error) {
        root.classList.add('welcome-experience--fallback');
        activeExperience.destroy();
        activeExperience = null;
        throw error;
    }
    return activeExperience;
}
