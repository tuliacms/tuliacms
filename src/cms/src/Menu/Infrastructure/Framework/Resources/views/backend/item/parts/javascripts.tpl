<script nonce="{{ csp_nonce() }}">
    let persistMode = '{{ persistMode }}';

    $(function () {
        $('#menu_item_form_type').change(function () {
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

        if (persistMode === 'create') {
            $('#menu_item_form_type').trigger('change');
        }
    });

    let updateIdentityField = function (type, identity) {
        let currentType = $('#menu_item_form_type').val();

        if (currentType !== type) {
            return;
        }

        $('#menu_item_form_identity').val(identity);
    };


</script>
