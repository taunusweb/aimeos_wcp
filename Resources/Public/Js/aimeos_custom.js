/* Autocomplete 1590fe0 https://github.com/kraaden/autocomplete (MIT License) */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e="undefined"!=typeof globalThis?globalThis:e||self).autocomplete=t()}(this,(function(){"use strict";return function(e){var t,n,o=document,i=e.container||o.createElement("div"),r=i.style,f=navigator.userAgent,l=~f.indexOf("Firefox")&&~f.indexOf("Mobile"),s=e.debounceWaitMs||0,a=e.preventSubmit||!1,u=e.disableAutoSelect||!1,d=l?"input":"keyup",c=[],p="",v=2,g=e.showOnFocus,m=0;if(void 0!==e.minLength&&(v=e.minLength),!e.input)throw new Error("input undefined");var h=e.input;function E(){n&&window.clearTimeout(n)}function w(){return!!i.parentNode}function y(){var e;m++,c=[],p="",t=void 0,(e=i.parentNode)&&e.removeChild(i)}function L(){for(;i.firstChild;)i.removeChild(i.firstChild);var n=function(e,t){var n=o.createElement("div");return n.textContent=e.label||"",n};e.render&&(n=e.render);var f=function(e,t){var n=o.createElement("div");return n.textContent=e,n};e.renderGroup&&(f=e.renderGroup);var l=o.createDocumentFragment(),s="#9?$";if(c.forEach((function(o){if(o.group&&o.group!==s){s=o.group;var i=f(o.group,p);i&&(i.className+=" group",l.appendChild(i))}var r=n(o,p);r&&(r.addEventListener("click",(function(t){e.onSelect(o,h),y(),t.preventDefault(),t.stopPropagation()})),o===t&&(r.className+=" selected"),l.appendChild(r))})),i.appendChild(l),c.length<1){if(!e.emptyMsg)return void y();var a=o.createElement("div");a.className="empty",a.textContent=e.emptyMsg,i.appendChild(a)}i.parentNode||o.body.appendChild(i),function(){if(w()){r.height="auto",r.width=h.offsetWidth+"px";var t,n=0;f(),f(),e.customize&&t&&e.customize(h,t,i,n)}function f(){var e=o.documentElement,i=e.clientTop||o.body.clientTop||0,f=e.clientLeft||o.body.clientLeft||0,l=window.pageYOffset||e.scrollTop,s=window.pageXOffset||e.scrollLeft,a=(t=h.getBoundingClientRect()).top+h.offsetHeight+l-i,u=t.left+s-f;r.top=a+"px",r.left=u+"px",(n=window.innerHeight-(t.top+h.offsetHeight))<0&&(n=0),r.top=a+"px",r.bottom="",r.left=u+"px",r.maxHeight=n+"px"}}(),function(){var e=i.getElementsByClassName("selected");if(e.length>0){var t=e[0],n=t.previousElementSibling;if(n&&-1!==n.className.indexOf("group")&&!n.previousElementSibling&&(t=n),t.offsetTop<i.scrollTop)i.scrollTop=t.offsetTop;else{var o=t.offsetTop+t.offsetHeight,r=i.scrollTop+i.offsetHeight;o>r&&(i.scrollTop+=o-r)}}}()}function b(){w()&&L()}function T(){b()}function x(e){e.target!==i?b():e.preventDefault()}function C(t){for(var n=t.which||t.keyCode||0,o=0,i=e.keysToIgnore||[38,13,27,39,37,16,17,18,20,91,9];o<i.length;o++){if(n===i[o])return}n>=112&&n<=123&&!e.keysToIgnore||40===n&&w()||S(0)}function k(n){var o=n.which||n.keyCode||0;if(38===o||40===o||27===o){var i=w();if(27===o)y();else{if(!i||c.length<1)return;38===o?function(){if(c.length<1)t=void 0;else if(t===c[0])t=c[c.length-1];else for(var e=c.length-1;e>0;e--)if(t===c[e]||1===e){t=c[e-1];break}}():function(){if(c.length<1&&(t=void 0),t&&t!==c[c.length-1]){for(var e=0;e<c.length-1;e++)if(t===c[e]){t=c[e+1];break}}else t=c[0]}(),L()}return n.preventDefault(),void(i&&n.stopPropagation())}13===o&&(t&&(e.onSelect(t,h),y()),a&&n.preventDefault())}function N(){g&&S(1)}function S(o){var i=++m,r=h.value,f=h.selectionStart||0;r.length>=v||1===o?(E(),n=window.setTimeout((function(){e.fetch(r,(function(e){m===i&&e&&(p=r,t=(c=e).length<1||u?void 0:c[0],L())}),o,f)}),0===o?s:0)):y()}function D(){setTimeout((function(){o.activeElement!==h&&y()}),200)}return i.className="autocomplete "+(e.className||""),r.position="absolute",i.addEventListener("mousedown",(function(e){e.stopPropagation(),e.preventDefault()})),i.addEventListener("focus",(function(){return h.focus()})),h.addEventListener("keydown",k),h.addEventListener(d,C),h.addEventListener("blur",D),h.addEventListener("focus",N),window.addEventListener("resize",T),o.addEventListener("scroll",x,!0),{destroy:function(){h.removeEventListener("focus",N),h.removeEventListener("keydown",k),h.removeEventListener(d,C),h.removeEventListener("blur",D),window.removeEventListener("resize",T),o.removeEventListener("scroll",x,!0),E(),y()}}}}));


AimeosCatalogFilter.setupSearchAutocompletion = function() {

    $(".catalog-filter-search .value").each((idx, el) => {
        const url = $(el).data("url");

        autocomplete({
            input: el,
            debounceWaitMs: 200,
            minLength: AimeosCatalogFilter.MIN_INPUT_LEN,
            fetch: function(text, update) {
                fetch(url + '&ai[f_search]=' + encodeURIComponent(text)).then(response => {
                    return response.json();
                }).then(data => {
                    update(data);
                });
            },
            render: function(item) {
                return $(item.html.trim()).get(0);
            },
            customize: function(input, inputRect, container, maxHeight) {
                const cat = $('<div class="catalog-suggest"/>');
                const prod = $('<div class="product-suggest"/>');

                $('.suggest-catalog', container).appendTo(cat);
                $('.suggest-product', container).appendTo(prod);

                $(container).append(cat).append(prod);
            }
        });
    });
};
