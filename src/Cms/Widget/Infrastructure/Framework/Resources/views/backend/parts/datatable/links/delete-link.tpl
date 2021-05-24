<a
    href=""
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteWidget'|trans({}, 'widgets') }}"
    data-component="action"
        data-settings="{
            'action': 'delete',
            'url': '{{ path('backend.widget.delete') }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('widget.delete') }}'
        }"
>
    <i class="dropdown-icon fas fa-times"></i>
    {{ 'deleteWidget'|trans({}, 'widgets') }}
</a>
