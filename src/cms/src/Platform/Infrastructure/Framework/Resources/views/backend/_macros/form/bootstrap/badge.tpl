{% macro errors_count(form, fields) %}
    {% set errors = 0 %}
    {% for field in fields %}
        {% if form[field] is defined and form[field].vars.errors|length %}
            {% set errors = errors + 1 %}
        {% endif %}
    {% endfor %}

    {% if errors %}
        <span class="badge badge-danger">{{ errors }}</span>
    {% endif %}
{% endmacro %}
