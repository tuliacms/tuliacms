{% set name %}
    {% if row.name is defined and row.name is not empty %}
        {{ row.name }}, [{{ row.username }}]
    {% else %}
        {{ row.username }}
    {% endif %}
{% endset %}

<a href="{{ path('backend.user.edit', { id: row.id }) }}" title="{{ name }}" class="link-title">
    {{ name }}
    <br /><span class="slug">{{ 'emailAddress'|trans({ email: row.email }, 'users') }}</span>
</a>
