<a href="{{ path('backend.form.edit', { id: row.id }) }}" title="{{ row.name }}" class="link-title">
    {% if row.translated is defined and row.translated != '1' %}
        <span class="badge badge-info" data-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
    {% endif %}
    {{ row.name }}
</a>
