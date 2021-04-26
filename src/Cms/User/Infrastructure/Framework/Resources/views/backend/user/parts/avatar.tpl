<div class="card" style="width:500px;max-width:100%;margin:0 auto">
    <div class="card-header">
        {{ 'avatar'|trans }}
    </div>
    <div class="card-body text-center">
        {% if user.avatar %}
            <img src="{{ asset(user.avatar) }}" alt="{{ 'avatar'|trans }}" class="img-thumbnail" />
            {{ form_row(form.remove_avatar, {label_attr: {class: 'checkbox-custom'} }) }}
        {% else %}
            <p class="mb-0">{{ 'userHasNoAvatar'|trans({}, 'users') }}</p>
            {#
                We need to render remove_avatar field even if the avatar not exists
                to prevent render this field in form_rest() function call.
            #}
            <div class="d-none">{{ form_row(form.remove_avatar) }}</div>
        {% endif %}
    </div>
    <div class="card-footer pb-0">
        {{ form_row(form.avatar) }}
    </div>
</div>
