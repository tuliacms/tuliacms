<a
    href="#"
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteItem'|trans({}, 'menu') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.term.delete', { taxonomyType: row.type }) }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('term.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteItem'|trans({}, 'menu') }}</a>
