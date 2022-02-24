{% block user_avatar_widget -%}
    <div class="text-center">
        {% if data and data.previewPath %}
            <img src="{{ asset(data.previewPath) }}" alt="{{ 'avatar'|trans }}" class="img-thumbnail mb-3" />
        {% else %}
            <p class="mb-3">{{ 'userHasNoAvatar'|trans({}, 'users') }}</p>
        {% endif %}
        {{- block('form_widget_simple') -}}
    </div>
{%- endblock user_avatar_widget %}
