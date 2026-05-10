(function () {
    var STAGGER_STEP = 35;
    var STAGGER_MAX  = 280;

    var content  = document.getElementById('realisations-content');
    var loading  = document.getElementById('realisations-loading');
    var filters  = document.querySelectorAll('.realisation-archive-filter');

    if (!content) return;

    /* ── État courant ──────────────────────────────────────────────────── */
    var params      = new URLSearchParams(window.location.search);
    var currentOcc  = params.get('occasion') || '';
    var isFetching  = false;

    /* ── Helpers ───────────────────────────────────────────────────────── */

    function setLoading(on) {
        if (!loading) return;
        loading.style.opacity         = on ? '1'    : '0';
        loading.style.pointerEvents   = on ? 'auto' : 'none';
    }

    function updatePills(occasion) {
        filters.forEach(function (btn) {
            var active = btn.dataset.occasion === occasion;
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
            btn.classList.toggle('bg-celya-primary',   active);
            btn.classList.toggle('text-white',          active);
            btn.classList.toggle('text-celya-primary', !active);
        });
    }

    function animateIn(container) {
        var cards = container.querySelectorAll('.realisation-archive-grid article');

        cards.forEach(function (card) {
            card.style.opacity   = '0';
            card.style.transform = 'translateY(14px)';
            card.style.transition = 'none';
        });

        /* forcer le reflow avant d'appliquer la transition */
        if (cards.length) void cards[0].offsetWidth;

        cards.forEach(function (card, i) {
            setTimeout(function () {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                card.style.opacity    = '1';
                card.style.transform  = '';
            }, Math.min(i * STAGGER_STEP, STAGGER_MAX));
        });
    }

    function pushState(occasion, pg) {
        var url = new URL(window.location.href);
        if (occasion) {
            url.searchParams.set('occasion', occasion);
        } else {
            url.searchParams.delete('occasion');
        }
        if (pg > 1) {
            url.searchParams.set('pg', pg);
        } else {
            url.searchParams.delete('pg');
        }
        history.pushState({ occasion: occasion, pg: pg }, '', url.toString());
    }

    /* ── Fetch ─────────────────────────────────────────────────────────── */

    function fetch(occasion, pg) {
        if (isFetching) return;
        isFetching = true;

        setLoading(true);

        var form = new FormData();
        form.append('action',   'celya_realisations');
        form.append('nonce',    celyaData.nonce);
        form.append('occasion', occasion || '');
        form.append('pg',       pg       || 1);

        /* Fade-out discret du contenu actuel */
        content.style.transition = 'opacity 0.2s ease';
        content.style.opacity    = '0.35';

        window.fetch(celyaData.ajaxUrl, { method: 'POST', body: form })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) throw new Error('AJAX error');

                content.innerHTML         = data.data.html;
                content.style.transition  = 'none';
                content.style.opacity     = '1';

                animateIn(content);

                /* Scroll vers le haut de la zone */
                var filters_el = document.getElementById('realisations-filters');
                if (filters_el) {
                    filters_el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(function () {
                content.style.opacity = '1';
            })
            .finally(function () {
                setLoading(false);
                isFetching = false;
            });
    }

    /* ── Filtres ───────────────────────────────────────────────────────── */

    filters.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var occ = this.dataset.occasion;
            if (occ === currentOcc) return;

            currentOcc = occ;
            updatePills(occ);
            pushState(occ, 1);
            fetch(occ, 1);
        });
    });

    /* ── Pagination (délégation sur #realisations-content) ─────────────── */

    content.addEventListener('click', function (e) {
        var btn = e.target.closest('.pagination-btn');
        if (!btn) return;

        var pg = parseInt(btn.dataset.page, 10);
        pushState(currentOcc, pg);
        fetch(currentOcc, pg);
    });

    /* ── Retour / avance navigateur ─────────────────────────────────────── */

    window.addEventListener('popstate', function (e) {
        var state = e.state || {};
        currentOcc = state.occasion || '';
        var pg     = state.pg       || 1;

        updatePills(currentOcc);
        fetch(currentOcc, pg);
    });

    /* ── Animation initiale au chargement ───────────────────────────────── */
    animateIn(content);

}());
