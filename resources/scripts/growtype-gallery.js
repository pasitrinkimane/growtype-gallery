import {storyLoader} from './partials/loaders/story-loader';
import {initGrowtypeMasonry} from "./plugins/masonry";

jQuery(document).ready(function () {
    initGrowtypeMasonry();
    storyLoader();
})
