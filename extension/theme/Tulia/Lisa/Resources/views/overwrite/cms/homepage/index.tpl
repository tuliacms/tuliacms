{% extends 'theme' %}

{% block content %}
    {% if widgets_space_count('homepage') %}
        <div class="homepage-widgets">
            {{ widgets_space('homepage') }}
        </div>
    {% else %}
        Empty homepage
    {% endif %}
{% endblock %}
