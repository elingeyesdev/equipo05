/**
 * Evita que el sidebar "salte" al navegar: conserva la posición de scroll.
 */
(function () {
    'use strict';

    var SCROLL_KEY = 'platform.sidebar.scrollTop';

    function sidebarEl() {
        return document.querySelector('.main-sidebar .sidebar');
    }

    function saveScroll() {
        var sidebar = sidebarEl();
        if (sidebar) {
            sessionStorage.setItem(SCROLL_KEY, String(sidebar.scrollTop));
        }
    }

    function restoreScroll() {
        var sidebar = sidebarEl();
        if (!sidebar) {
            return;
        }

        var saved = sessionStorage.getItem(SCROLL_KEY);
        if (saved !== null && saved !== '') {
            sidebar.scrollTop = parseInt(saved, 10) || 0;
            return;
        }

        var active = document.querySelector('.main-sidebar .nav-link.active');
        if (active) {
            active.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        restoreScroll();

        document.querySelectorAll('.main-sidebar a[href]').forEach(function (link) {
            var href = link.getAttribute('href');
            if (!href || href === '#' || href.indexOf('javascript:') === 0) {
                return;
            }
            link.addEventListener('click', saveScroll);
        });
    });
})();
