$(function () {
    let toolbar = $('#profiler-toolbar');
    let body = $('body');
    const bodyClass = 'tulia-profiler-toolbar-opened';
    const toolbarHidden = 'profiler-toolbar-hidden';
    const toolbarSettingKey = 'tulia-profiler.toolbar-hidden';

    toolbar.load(__profiler_toolbar_route, function () {
        if (localStorage.getItem(toolbarSettingKey) !== '1') {
            toolbar.removeClass(toolbarHidden);
            body.addClass(bodyClass);
        }
    });

    toolbar.on('click', '.profiler-toolbar-close', function () {
        toolbar.toggleClass(toolbarHidden);

        if (toolbar.hasClass(toolbarHidden)) {
            localStorage.setItem(toolbarSettingKey, '1');
            body.removeClass(bodyClass);
        } else {
            localStorage.setItem(toolbarSettingKey, null);
            body.addClass(bodyClass);
        }
    });
});
