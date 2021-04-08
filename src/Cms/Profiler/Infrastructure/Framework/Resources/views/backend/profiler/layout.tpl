{% block toolbar %}
    {% set icon %} {% endset %}
    {% set text %} {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false }) }}
{% endblock %}
