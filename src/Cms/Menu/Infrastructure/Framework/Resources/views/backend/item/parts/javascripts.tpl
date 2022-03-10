<script nonce="{{ csp_nonce() }}">
    $(function () {
        $('#content_builder_form_menu_item_type').change(function () {
            let container = $('.menu-item-type')
                .addClass('d-none')
                .filter('[data-type="' + $(this).val() + '"]')
                .removeClass('d-none');

            let identity = container.find('[data-identity="' + $(this).val() + '"]').val();

            updateIdentityField($(this).val(), identity);

            setTimeout(function () {
                container.find('.item-type-field-autofocus').focus();
            }, 100);
        });

        $('[data-identity]').on('change keyup keydown blur', function () {
            updateIdentityField($(this).attr('data-identity'), $(this).val());
        });
    });

    let updateIdentityField = function (type, identity) {
        let currentType = $('#content_builder_form_menu_item_type').val();

        if (currentType !== type) {
            return;
        }

        $('#content_builder_form_menu_item_identity').val(identity);
    };
</script>
