$(function () {
    let body = $('body');

    if (window.Cookies && Cookies.get('tulia-toolbar-opened') === 'yes') {
        body.addClass('tulia-toolbar-opened');
    }

    body.find('.tulia-fisher').click(function () {
        body.toggleClass('tulia-toolbar-opened');

        let opened;

        if (body.hasClass('tulia-toolbar-opened')) {
            opened = 'yes';
        } else {
            opened = 'no';
        }

        if (window.Cookies) {
            Cookies.set('tulia-toolbar-opened', opened, {expires: 365, path: '/'});
        }
    });
});
