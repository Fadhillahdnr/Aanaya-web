import { MusicExperience } from './MusicExperience';

let activeMusicExperience = null;

export function initMusicExperience(root) {
    if (!root || activeMusicExperience) return activeMusicExperience;
    activeMusicExperience = new MusicExperience(root);
    activeMusicExperience.init();
    return activeMusicExperience;
}
