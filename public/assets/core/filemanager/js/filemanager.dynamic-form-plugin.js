Tulia.DynamicForm.plugin('filemanager', {
    on: {
        'open-filemanager': function (button) {
            const fieldId = button.attr('data-input-target');
            const filter = button.attr('data-filemanager-filter');

            Tulia.Filemanager.create({
                targetInput: '#' + fieldId,
                showOnInit: true,
                endpoint: button.attr('data-filemanager-endpoint'),
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
