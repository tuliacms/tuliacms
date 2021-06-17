<a
    href="#"
    class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
    title="{{ 'deleteForm'|trans({}, 'contact-form') }}"
    data-component="action"
    data-settings="{
        'action': 'delete',
        'url': '{{ path('backend.contact_form.delete') }}',
        'data': {
            'ids': ['{{ row.id }}']
        },
        'csrf_token': '{{ csrf_token('form.delete') }}'
    }"
><i class="dropdown-icon fas fa-times"></i> {{ 'deleteForm'|trans({}, 'contact-form') }}</a>
