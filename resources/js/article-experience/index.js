import { ArticleExperience } from './ArticleExperience';

let activeExperience = null;

export function initArticleExperience(root) {
    if (!root || activeExperience) {
        return activeExperience;
    }

    activeExperience = new ArticleExperience(root);
    activeExperience.init();

    return activeExperience;
}
