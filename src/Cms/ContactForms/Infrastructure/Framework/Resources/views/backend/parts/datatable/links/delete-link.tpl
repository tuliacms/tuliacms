<a
    href="#"
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteForm'|trans({}, 'forms') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.form.delete') }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('form.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteForm'|trans({}, 'forms') }}</a>
