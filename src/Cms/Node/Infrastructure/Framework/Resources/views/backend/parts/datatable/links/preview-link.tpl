{% if row.status == 'published' %}
    {% set path = node_path_from_id(row.id) %}
    {% if path %}
        <a class="dropdown-item dropdown-item-with-icon action-element-single-auto-form" href="{{ path }}" target="_blank" title="{{ 'previewNode'|trans({}, 'node') }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'previewNode'|trans({}, 'node') }}</a>
    {% endif %}
{% endif %}
