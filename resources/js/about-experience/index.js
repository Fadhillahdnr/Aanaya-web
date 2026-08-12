import { AboutExperience } from './AboutExperience';

export function initAboutExperience(root) {
    if (!root) return null;
    const experience = new AboutExperience(root);
    experience.init();
    return experience;
}
