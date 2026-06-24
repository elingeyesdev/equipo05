/**
 * Resuelve coordenadas a nombres de lugar vía API interna (Nominatim + caché).
 */
window.OsmPlaceResolver = (function () {
    const memoryCache = new Map();
    const pending = new Map();
    let apiUrl = '/api/geocode/lugar';

    function cacheKey(lat, lng) {
        return Number(lat).toFixed(5) + ',' + Number(lng).toFixed(5);
    }

    function configure(options) {
        if (options && options.apiUrl) {
            apiUrl = options.apiUrl;
        }
    }

    function resolve(lat, lng) {
        const key = cacheKey(lat, lng);
        if (memoryCache.has(key)) {
            return Promise.resolve(memoryCache.get(key));
        }
        if (pending.has(key)) {
            return pending.get(key);
        }

        const promise = fetch(apiUrl + '?lat=' + encodeURIComponent(lat) + '&lng=' + encodeURIComponent(lng), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                const lugar = data.lugar || 'Ubicación no identificada';
                memoryCache.set(key, lugar);
                pending.delete(key);
                return lugar;
            })
            .catch(function () {
                pending.delete(key);
                const fallback = 'Ubicación no identificada';
                memoryCache.set(key, fallback);
                return fallback;
            });

        pending.set(key, promise);
        return promise;
    }

    function bindElements(root) {
        const scope = root || document;
        scope.querySelectorAll('.osm-place[data-lat][data-lng]').forEach(function (el) {
            if (el.dataset.osmResolved === '1') return;
            el.dataset.osmResolved = '1';

            const lat = parseFloat(el.dataset.lat);
            const lng = parseFloat(el.dataset.lng);
            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                el.textContent = 'Sin ubicación';
                return;
            }

            resolve(lat, lng).then(function (lugar) {
                el.textContent = lugar;
                el.classList.remove('osm-place--loading');
            });
        });
    }

    return {
        configure: configure,
        resolve: resolve,
        bindElements: bindElements,
    };
})();

document.addEventListener('DOMContentLoaded', function () {
    window.OsmPlaceResolver.bindElements(document);
});
