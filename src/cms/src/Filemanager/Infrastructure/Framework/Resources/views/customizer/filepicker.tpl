{% assets ['filemanager'] %}

<div class="form-group {{ multilingual ? ' form-group-multilingual' : '' }}">
    <label class="customizer-label">{{ label }}</label>
    <div class="input-group">
        <input type="text" id="{{ control_id }}" name="{{ control_name }}" class="customizer-control form-control" value="{{ value }}" data-transport="{{ transport }}" />
        <div class="input-group-append">
            <button class="btn btn-default btn-icon-only" type="button">
                <i class="btn-icon fas fa-folder-open"></i>
            </button>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
    $(function () {
        Tulia.Filemanager.create({
            targetInput: '#{{ control_id }}',
            openTrigger: $('#{{ control_id }}').closest('.input-group').find('button'),
            endpoint: '{{ path('backend.filemanager.endpoint') }}',
            filter: {
                type: '{{ file_type ?? '*' }}',
            },
            multiple: false,
            closeOnSelect: true,
            onSelect: function (files) {
                if (!files.length) {
                    return;
                }

                $('#{{ control_id }}')
                    .val(files[0].id)
                    .trigger('change');
            }
        });
    });
</script>
