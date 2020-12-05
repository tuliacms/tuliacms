{% extends '@backend/profiler/profiler/layout.tpl' %}

{% block toolbar %}
    {% set total_time = '%.0f'|format(collector.duration) %}
    {% set status_color = collector.duration ? 'yellow' : '' %}

    {% set icon %}
        {{ include('@backend/profiler/icon/time.svg') }}
        <span class="tulia-toolbar-value">{{ total_time }}</span>
        <span class="tulia-toolbar-label">ms</span>
    {% endset %}

    {% set text %}
        <div class="tulia-toolbar-info-group">
            <div class="tulia-toolbar-info-piece">
                <b>Total time</b>
                <span>{{ total_time }} ms</span>
            </div>
        </div>
    {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false }) }}
{% endblock %}
