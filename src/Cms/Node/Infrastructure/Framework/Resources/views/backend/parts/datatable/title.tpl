<a href="{{ path('backend.node.edit', { node_type: row.type, id: row.id }) }}" class="link-title" title="{{ row.title }}">
    <span class="boxur-depth boxur-depth-{{ row.level }}">
        {% if row.translated is defined and row.translated != '1' %}
            <span class="badge badge-info" data-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
        {% endif %}
        {% if row.status == 'sketch' %}
            <span class="badge badge-secondary"><i class="dropdown-icon fas fa-pen-alt"></i> &nbsp;{{ 'sketch'|trans }}</span>
        {% elseif row.status == 'trashed' %}
            <span class="badge badge-warning"><i class="dropdown-icon fas fa-trash"></i> &nbsp;{{ 'trashed'|trans }}</span>
        {% endif %}
        <span class="node-title">{{ row.title }}</span>
        <br />
        <span class="slug">{{ 'slugValue'|trans({ slug: row.slug }) }}</span>
        {% if row.flags is not empty %}
            <br />
            {% for flag in row.flags %}
                {% set flagName = trans_exists('flagType.' ~ flag, {}, 'node')
                    ? ('flagType.' ~ flag)|trans({}, 'node')
                    : flag %}
                <span class="badge badge-secondary">
                    {{ 'flagWithName'|trans({ flag: flagName }, 'node') }}
                </span>
            {% endfor %}
        {% endif %}
    </span>
</a>
