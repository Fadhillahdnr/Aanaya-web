import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import './direct-cloudinary-upload';
import './media-performance';
import './profile-camera';
import './product-image-admin';
import './product-variant-admin';
import './product-gallery';
import './product-variant-selection';

const articleExperienceRoot = document.querySelector('[data-article-experience]');

if (articleExperienceRoot) {
    import('./article-experience/index.js')
        .then(({ initArticleExperience }) => initArticleExperience(articleExperienceRoot))
        .catch((error) => {
            console.warn('Aanaya cinematic article enhancement could not start.', error);
        });
}

const dashboardExperienceRoot = document.querySelector('[data-dashboard-experience]');

if (dashboardExperienceRoot) {
    import('./dashboard-experience/index.js')
        .then(({ initDashboardExperience }) => initDashboardExperience(dashboardExperienceRoot))
        .catch((error) => {
            console.warn('Aanaya dashboard enhancement could not start.', error);
        });
}

const welcomeExperienceRoot = document.querySelector('[data-welcome-experience]');

if (welcomeExperienceRoot) {
    import('./welcome-experience/index.js')
        .then(({ initWelcomeExperience }) => initWelcomeExperience(welcomeExperienceRoot))
        .catch((error) => {
            welcomeExperienceRoot.classList.add('welcome-experience--fallback');
            console.warn('Aanaya welcome enhancement could not start.', error);
        });
}

const authExperienceRoot = document.querySelector('[data-auth-experience]');

if (authExperienceRoot) {
    import('./auth-experience/index.js')
        .then(({ initAuthExperience }) => initAuthExperience(authExperienceRoot))
        .catch((error) => console.warn('Aanaya auth enhancement could not start.', error));
}
