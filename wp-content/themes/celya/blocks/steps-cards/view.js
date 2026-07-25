(function () {
    var CARD_DURATION      = 100;  // ms — doit correspondre à la transition CSS
    var CONNECTOR_DURATION = 250;  // ms — doit correspondre à la transition CSS
    var GAP                = 40;   // pause entre chaque étape

    document.querySelectorAll('.celya-steps-cards').forEach(function (container) {
        var cards        = container.querySelectorAll('.celya-steps-card');
        var hasConnector = container.classList.contains('celya-steps-cards--has-connector');

        if (!cards.length) return;

        container.classList.add('celya-steps-cards--will-animate');

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                observer.unobserve(entry.target);
                animateSequence(cards, hasConnector);
            });
        }, { threshold: 0.2 });

        observer.observe(container);
    });

    function animateSequence(cards, hasConnector) {
        var delay = 0;

        Array.prototype.forEach.call(cards, function (card, index) {
            setTimeout(showCard.bind(null, card), delay);

            if (index >= cards.length - 1) return;

            if (hasConnector) {
                delay += CARD_DURATION + GAP;
                setTimeout(showConnector.bind(null, cards[index + 1]), delay);
                delay += CONNECTOR_DURATION + GAP;
            } else {
                delay += Math.round(CARD_DURATION * 0.55) + GAP;
            }
        });
    }

    function showCard(card) {
        card.classList.add('celya-steps-card--animate-visible');
    }

    function showConnector(card) {
        card.classList.add('celya-steps-card--connector-visible');
    }
}());
