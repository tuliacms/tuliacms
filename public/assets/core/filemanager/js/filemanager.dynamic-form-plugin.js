Tulia.DynamicForm.plugin('filemanager', {
    on: {
        'open-filemanager': function () {
            const fieldId = $(this).attr('data-input-target');
            const filter = $(this).attr('data-filemanager-filter');

            Tulia.Filemanager.create({
                targetInput: '#' + fieldId,
                showOnInit: true,
                endpoint: $(this).attr('data-filemanager-endpoint'),
                filter: {
                    type: filter === '*' ? '*' : JSON.parse(filter)
                },
                multiple: false,
                closeOnSelect: true,
                onSelect: function (files) {
                    if (!files.length) {
                        return;
                    }

                    $('#' + fieldId).val(files[0].id);
                }
            });
        },
    }
});
