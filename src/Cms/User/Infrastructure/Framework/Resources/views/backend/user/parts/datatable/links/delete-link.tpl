<a
    href="#"
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteUser'|trans({}, 'users') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.user.delete') }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('user.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteUser'|trans({}, 'users') }}</a>
