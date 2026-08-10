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
