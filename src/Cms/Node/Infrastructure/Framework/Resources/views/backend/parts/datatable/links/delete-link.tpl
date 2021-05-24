{% if row.status == 'trashed' %}
    {{ _self.publish(row, nodeType) }}
    {{ _self.delete(row, nodeType) }}
{% endif %}

{% if row.status == 'published' %}
    {{ _self.delete(row, nodeType) }}
{% endif %}

{% if row.status == 'sketch' %}
    {{ _self.publish(row, nodeType) }}
    {{ _self.trash(row, nodeType) }}
{% endif %}



{% macro publish(row, nodeType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon"
        title="{{ 'publishNode'|trans({}, nodeType.translationDomain) }}"
        data-component="action"
        data-settings="{
            'action': 'confirm',
            'action_headline': '{{ 'publishSelectedNodes'|trans({}, nodeType.translationDomain) }}',
            'action_question': '{{ 'areYouSureYouWantToPublishFollowingNodes'|trans({}, nodeType.translationDomain) }}',
            'url': '{{ path('backend.node.change_status', { status: 'published' }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.change-status') }}'
        }"
    ><i class="dropdown-icon fas fa-check"></i> {{ 'publishNode'|trans({}, nodeType.translationDomain) }}</a>
{% endmacro %}

{% macro trash(row, nodeType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon"
        title="{{ 'deleteNode'|trans({}, nodeType.translationDomain) }}"
        data-component="action"
        data-settings="{
            'action': 'confirm',
            'action_headline': '{{ 'trashSelectedNodes'|trans({}, nodeType.translationDomain) }}',
            'action_question': '{{ 'areYouSureYouWantToTrashFollowingNodes'|trans({}, nodeType.translationDomain) }}',
            'url': '{{ path('backend.node.change_status', { status: 'trashed' }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.change-status') }}'
        }"
    ><i class="dropdown-icon fas fa-trash"></i> {{ 'trashNode'|trans({}, nodeType.translationDomain) }}</a>
{% endmacro %}

{% macro delete(row, nodeType) %}
    <a
        href="#"
        class="dropdown-item dropdown-item-with-icon dropdown-item-danger"
        title="{{ 'deleteNode'|trans({}, nodeType.translationDomain) }}"
        data-component="action"
        data-settings="{
            'action': 'delete',
            'url': '{{ path('backend.node.delete', { node_type: row.type }) }}',
            'data': {
                'ids': ['{{ row.id }}']
            },
            'csrf_token': '{{ csrf_token('node.delete') }}'
        }"
    ><i class="dropdown-icon fas fa-times"></i> {{ 'deleteNode'|trans({}, nodeType.translationDomain) }}</a>
{% endmacro %}

