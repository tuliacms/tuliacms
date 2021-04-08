<div class="tulia-toolbar-block tulia-toolbar-block-{{ name }} tulia-toolbar-status-{{ status|default('normal') }} {{ additional_classes|default('') }}" {{ block_attrs|default('')|raw }}>
    {% if link is not defined or link %}<a href="{{ url('_profiler', { token: token, panel: name }) }}">{% endif %}
        <div class="tulia-toolbar-icon">{{ icon|default('') }}</div>
    {% if link|default(false) %}</a>{% endif %}
    <div class="tulia-toolbar-info">{{ text|default('') }}</div>
</div>
