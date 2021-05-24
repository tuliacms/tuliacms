<a
    href="#"
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteItem'|trans({}, 'menu') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.menu.item.delete', { menuId: row.menu_id }) }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('menu.item.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteItem'|trans({}, 'menu') }}</a>
