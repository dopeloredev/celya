(function () {
    var FADE_DURATION = 220; // doit correspondre à la transition CSS (0.22s)
    var STAGGER_STEP  = 40;  // délai entre chaque carte à l'apparition
    var STAGGER_MAX   = 200; // plafond du stagger pour les grandes grilles

    document.querySelectorAll('.wp-block-celya-realisations').forEach(function (block) {
        var pills = block.querySelectorAll('.realisation-filter-pill');
        var items = block.querySelectorAll('.realisation-item');
        var empty = block.querySelector('.realisation-empty');
        var grid  = block.querySelector('.realisation-grid');
        var timer = null;

        if (!pills.length) return;

        pills.forEach(function (pill) {
            pill.addEventListener('click', function () {
                var filter = this.dataset.filter;

                pills.forEach(function (p) {
                    p.setAttribute('aria-pressed', p === pill ? 'true' : 'false');
                });

                clearTimeout(timer);

                var toHide = []; // visibles → doivent disparaître
                var toShow = []; // cachés   → doivent apparaître
                // items déjà dans le bon état (visibles et restent visibles) : rien à faire

                items.forEach(function (item) {
                    var occ     = item.dataset.occasions ? item.dataset.occasions.split(' ') : [];
                    var show    = filter === 'all' || occ.indexOf(filter) !== -1;
                    var visible = item.style.display !== 'none';

                    if (!show && visible)  toHide.push(item);
                    if (show  && !visible) toShow.push(item);
                });

                // Lancer le fade-out des items sortants
                toHide.forEach(function (item) {
                    item.style.opacity   = '0';
                    item.style.transform = 'scale(0.92)';
                });

                timer = setTimeout(function () {
                    // Cacher les items sortants
                    toHide.forEach(function (item) {
                        item.style.display   = 'none';
                        item.style.transform = '';
                    });

                    // Préparer les items entrants : les rendre invisibles avant de les afficher
                    toShow.forEach(function (item) {
                        item.style.display   = '';
                        item.style.opacity   = '0';
                        item.style.transform = 'scale(0.92)';
                    });

                    // Forcer un reflow pour que le navigateur enregistre l'état de départ
                    if (toShow.length) void toShow[0].offsetWidth;

                    // Fade-in en stagger
                    toShow.forEach(function (item, i) {
                        setTimeout(function () {
                            item.style.opacity   = '1';
                            item.style.transform = '';
                        }, Math.min(i * STAGGER_STEP, STAGGER_MAX));
                    });

                    // État vide
                    var totalVisible = Array.prototype.filter.call(items, function (item) {
                        return item.style.display !== 'none';
                    }).length;

                    if (grid)  grid.style.display = totalVisible === 0 ? 'none' : '';
                    if (empty) empty.classList.toggle('hidden', totalVisible !== 0);

                }, FADE_DURATION);
            });
        });
    });
}());
