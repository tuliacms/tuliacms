customizer.customized('hero.static.headline', function (value) {
    $('.hero-static .hero-text .hero-headline').text(value);
});

customizer.customized('hero.static.description', function (value) {
    $('.hero-static .hero-text .hero-description').text(value);
});

customizer.customized('hero.static.button.label', function (value) {
    $('.hero-static .hero-text button').text(value);
});

customizer.customized('hero.static.background', function (value) {
    $('.hero-static .hero-image').css('background-image', 'url(' + value + ')');
});

customizer.customized('lisa.footer.contact.phone', function (value) {
    $('.footer-contact .contact-row.contact-phone span').text(value);
});
