{% for name, template in templates %}
    {% if block('toolbar', template) is defined %}
        {% with {
            collector: profile.collector(name),
            token: profile.token,
            name: name
        } %}
            {{ block('toolbar', template) }}
        {% endwith %}
    {% endif %}
{% endfor %}

<div class="profiler-toolbar-close">
    {{ include('@backend/profiler/icon/eye.svg') }}
</div>
