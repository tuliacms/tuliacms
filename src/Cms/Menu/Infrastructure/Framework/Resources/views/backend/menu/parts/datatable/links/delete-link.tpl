<a
        href="#"
        class="dropdown-item-with-icon dropdown-item-danger"
        title="{{ 'deleteMenu'|trans({}, 'menu') }}"
        data-component="action"
        data-settings="{
            'action': 'delete',
            'url': '{{ path('backend.menu.delete') }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('menu.delete') }}'
        }"
>
    <i class="dropdown-icon fas fa-times"></i>
    {{ 'deleteMenu'|trans({}, 'menu') }}
</a>
