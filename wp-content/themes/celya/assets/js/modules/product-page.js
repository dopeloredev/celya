document.addEventListener('DOMContentLoaded', function () {

    /* =========================================
                Galerie d'images
    ========================================== */
    const thumbs    = document.querySelectorAll('.celya-thumb');
    const mainImg   = document.getElementById('celya-main-image');
    const zoomBtn   = document.getElementById('celya-zoom-btn');

    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            const fullUrl = this.dataset.full;

            // Changer l'image principale
            mainImg.style.opacity = '0';
            setTimeout(function () {
                mainImg.src = fullUrl;
                mainImg.style.opacity = '1';
            }, 150);

            // Mettre à jour le lien zoom
            if (zoomBtn) zoomBtn.href = fullUrl;

            // Gérer l'état actif des miniatures
            thumbs.forEach(function (t) {
                t.classList.remove('active-thumb', 'border-celya-orange_dark', 'opacity-100');
                t.classList.add('border-transparent', 'opacity-60');
            });
            this.classList.add('active-thumb', 'border-celya-orange_dark', 'opacity-100');
            this.classList.remove('border-transparent', 'opacity-60');
        });
    });

    /* =========================================
                Formulaire d'avis
    ========================================== */
    const triggers = document.querySelectorAll('a[href="#review_form_wrapper"]');
    const wrapper  = document.getElementById('review_form_wrapper');

    if (!wrapper) return;

    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();

            if (wrapper.classList.contains('hidden')) {
                wrapper.classList.remove('hidden');
            }

            setTimeout(function () {
                wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 50);
        });
    });
});