{% if row.status == 'trashed' %}
    {{ _self.publish(row, contentType) }}
    {{ _self.delete(row, contentType) }}
{% endif %}

{% if row.status == 'published' %}
    {{ _self.delete(row, contentType) }}
{% endif %}

{% if row.status == 'sketch' %}
    {{ _self.publish(row, contentType) }}
    {{ _self.trash(row, contentType) }}
{% endif %}



{% macro publish(row, contentType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon"
        title="{{ 'publishNode'|trans({}, 'node') }}"
        data-component="action"
        data-settings="{
            'action': 'confirm',
            'action_headline': '{{ 'publishSelectedNodes'|trans({}, 'node') }}',
            'action_question': '{{ 'areYouSureYouWantToPublishFollowingNodes'|trans({}, 'node') }}',
            'url': '{{ path('backend.node.change_status', { status: 'published' }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.change-status') }}'
        }"
    ><i class="dropdown-icon fas fa-check"></i> {{ 'publishNode'|trans({}, 'node') }}</a>
{% endmacro %}

{% macro trash(row, contentType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon"
        title="{{ 'deleteNode'|trans({}, 'node') }}"
        data-component="action"
        data-settings="{
            'action': 'confirm',
            'action_headline': '{{ 'trashSelectedNodes'|trans({}, 'node') }}',
            'action_question': '{{ 'areYouSureYouWantToTrashFollowingNodes'|trans({}, 'node') }}',
            'url': '{{ path('backend.node.change_status', { status: 'trashed' }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.change-status') }}'
        }"
    ><i class="dropdown-icon fas fa-trash"></i> {{ 'trashNode'|trans({}, 'node') }}</a>
{% endmacro %}

{% macro delete(row, contentType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
        title="{{ 'deleteNode'|trans({}, 'node') }}"
        data-component="action"
        data-settings="{
            'action': 'delete',
            'url': '{{ path('backend.node.delete', { node_type: row.type }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.delete') }}'
        }"
    ><i class="dropdown-icon fas fa-times"></i> {{ 'deleteNode'|trans({}, 'node') }}</a>
{% endmacro %}

