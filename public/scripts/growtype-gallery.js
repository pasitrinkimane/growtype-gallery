!function(){"use strict";var e,r={661:function(){function e(){$=jQuery;function e(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,o=!(arguments.length>1&&void 0!==arguments[1])||arguments[1],a=e.closest(".growtype-gallery-wrapper").attr("id"),l=e.find(".loader");l.fadeOut(),e.find(".overlay").is(":visible")&&(t(a),e.find(".overlay").fadeOut(),o&&r(l))}function r(e){var r=e.closest(".growtype-gallery-wrapper").attr("id");window.growtypeGallery.loader[r].progress=0,e.find(".loader-inner").attr("data-progress","0"),e.fadeIn(),o(e)}function o(e){var r=e.find(".loader-inner"),t=e.closest(".growtype-gallery-wrapper").attr("id"),a=window.growtypeGallery.loader[t].progress,l=parseInt(a)+1;window.growtypeGallery.loader[t].progress=l,r.css("width",l+"%"),a>=0&&a<=99?setTimeout((function(){o(e)}),30):100===a&&(window.growtypeGallery.loader[t].progress=0,n(t))}function t(e){$("#"+e).find(".loader").fadeOut(),$("#"+e).find(".overlay").fadeIn()}function a(){$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').length>0&&$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').each((function(e,r){var o=$(r).attr("id");window.growtypeGallery.loader[o]={counter:0,progress:0,stopped:!1},l(o)}))}function l(e){window.growtypeGallery.loader[e].stopped||n(e)}function n(r){window.growtypeGallery.loader[r].counter>$("#"+r+'.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').length-1&&(window.growtypeGallery.loader[r].counter=0);var o=$("#"+r+'.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').find('.wp-block-image[data-index="'+window.growtypeGallery.loader[r].counter+'"]');o.length>0&&(e(o),o.hasClass("slick-slide")&&$("#"+r+'.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .slick-slider').slick("slickGoTo",window.growtypeGallery.loader[r].counter)),window.growtypeGallery.loader[r].counter++}function i(e){window.growtypeGallery.loader[e].stopped=!0,clearTimeout(window.growtypeGallery.loader[e].startStoryLoader),window.growtypeGallery.loader[e].progress=-1}window.growtypeGallery={loader:{}},function(){a()}(),$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').on("mouseenter touchstart",(function(){$(this).find(".slick-slider").length>0&&i($(this).attr("id"))})),$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').on("mouseenter touchstart",(function(){0===$(this).closest(".growtype-gallery-wrapper").find(".slick-slider").length&&i($(this).closest(".growtype-gallery-wrapper").attr("id"));e($(this),!1)})),$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"]').on("mouseleave touchend",(function(){if($(this).find(".slick-slider").length>0){var e=$(this).attr("id");window.growtypeGallery.loader[e].stopped=!1,window.growtypeGallery.loader[e].startStoryLoader=setTimeout((function(){l(e)}),1e3)}})),$('.growtype-gallery-wrapper.loader-active[data-loader-type="story"] .wp-block-image').on("mouseleave touchend",(function(){var e=$(this).closest(".growtype-gallery-wrapper").attr("id");window.growtypeGallery.loader[e].counter=parseInt($(this).closest(".wp-block-image").attr("data-index"))+1,window.growtypeGallery.loader[e].progress=0,t(e),0===$(this).closest(".growtype-gallery-wrapper").find(".slick-slider").length&&(window.growtypeGallery.loader[e].stopped=!1,window.growtypeGallery.loader[e].startStoryLoader=setTimeout((function(){l(e)}),1e3))}))}jQuery(document).ready((function(){e()}))},941:function(){},169:function(){}},o={};function t(e){var a=o[e];if(void 0!==a)return a.exports;var l=o[e]={exports:{}};return r[e](l,l.exports,t),l.exports}t.m=r,e=[],t.O=function(r,o,a,l){if(!o){var n=1/0;for(s=0;s<e.length;s++){o=e[s][0],a=e[s][1],l=e[s][2];for(var i=!0,d=0;d<o.length;d++)(!1&l||n>=l)&&Object.keys(t.O).every((function(e){return t.O[e](o[d])}))?o.splice(d--,1):(i=!1,l<n&&(n=l));if(i){e.splice(s--,1);var p=a();void 0!==p&&(r=p)}}return r}l=l||0;for(var s=e.length;s>0&&e[s-1][2]>l;s--)e[s]=e[s-1];e[s]=[o,a,l]},t.o=function(e,r){return Object.prototype.hasOwnProperty.call(e,r)},function(){var e={413:0,723:0,239:0};t.O.j=function(r){return 0===e[r]};var r=function(r,o){var a,l,n=o[0],i=o[1],d=o[2],p=0;if(n.some((function(r){return 0!==e[r]}))){for(a in i)t.o(i,a)&&(t.m[a]=i[a]);if(d)var s=d(t)}for(r&&r(o);p<n.length;p++)l=n[p],t.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return t.O(s)},o=self.webpackChunkplugin=self.webpackChunkplugin||[];o.forEach(r.bind(null,0)),o.push=r.bind(null,o.push.bind(o))}(),t.O(void 0,[723,239],(function(){return t(661)})),t.O(void 0,[723,239],(function(){return t(941)}));var a=t.O(void 0,[723,239],(function(){return t(169)}));a=t.O(a)}();
//# sourceMappingURL=growtype-gallery.js.map