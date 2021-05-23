<a href="{{ path('backend.node.edit', { node_type: row.type, id: row.id }) }}" class="link-title" title="{{ row.title }}">
    <span class="boxur-depth boxur-depth-{{ row.level }}">
        {% if row.translated is defined and row.translated != '1' %}
            <span class="badge badge-info" data-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
        {% endif %}
        {#{% if criteria.node_status is empty %}
            {% if row.status == 'sketch' %}
                <span class="badge badge-secondary"><i class="dropdown-icon fas fa-pen-alt"></i> &nbsp;{{ 'sketch'|trans }}</span>
            {% elseif row.status == 'trashed' %}
                <span class="badge badge-warning"><i class="dropdown-icon fas fa-trash"></i> &nbsp;{{ 'trashed'|trans }}</span>
            {% endif %}
        {% endif %}#}
        <span class="node-title">{{ row.title }}</span>
        <br />
        <span class="slug">{{ 'slugValue'|trans({ slug: row.slug }) }}</span>
    </span>
</a>
