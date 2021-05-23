<a
    href="#"
    class="dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteNode'|trans({}, 'pages') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.node.delete', { node_type: row.type }) }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('node.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteNode'|trans({}, 'pages') }}</a>
