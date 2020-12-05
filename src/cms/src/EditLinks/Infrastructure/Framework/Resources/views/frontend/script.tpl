{% assets ['js_cookie'] %}

<script nonce="{{ csp_nonce() }}">
    $('body').on('click', '.tulia-edit-links-toggle', function () {
        let show = Cookies.get('tulia-edit-links-show');

        if (show === 'yes') {
            Cookies.remove('tulia-edit-links-show');
        } else {
            Cookies.set('tulia-edit-links-show', 'yes', { expires: 365 });
        }

        document.location.reload();
    });
</script>
