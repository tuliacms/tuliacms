{% extends 'widget' %}

{% block content %}
    <div class="row">
        {% for feat in features %}
            <div class="col-sm text-center">
                {% if feat.icon %}
                    <div style="width: 100%; max-width: 60px; display: inline-block;">
                        {{ is_file_type(feat.icon, 'svg') ? svg(feat.icon) : image(feat.icon) }}
                    </div>
                {% endif %}
                <h5>{{ feat.label }}</h5>
                <p>{{ feat.description }}</p>
            </div>
        {% endfor %}
    </div>
{% endblock %}
