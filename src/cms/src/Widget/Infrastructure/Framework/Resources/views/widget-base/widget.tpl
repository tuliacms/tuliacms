<div {{ _self.attributes(widgetAttributes) }}>
    <div class="widget-item-inner">
        {% if widgetTitle %}
            <div class="widget-title">{{ widgetTitle }}</div>
        {% endif %}
        <div class="widget-content">
            {% block content %}{% endblock %}
        </div>
    </div>
</div>

{% macro attributes(attributes) %}
    {% for key, val in attributes %}
        {{ key }}="{{ val }}"
    {% endfor %}
{% endmacro %}
