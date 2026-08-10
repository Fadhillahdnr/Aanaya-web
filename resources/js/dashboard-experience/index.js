import { DashboardExperience } from './DashboardExperience';

let activeDashboardExperience = null;

export function initDashboardExperience(root) {
    if (!root || activeDashboardExperience) return activeDashboardExperience;
    activeDashboardExperience = new DashboardExperience(root);
    activeDashboardExperience.init();
    return activeDashboardExperience;
}
