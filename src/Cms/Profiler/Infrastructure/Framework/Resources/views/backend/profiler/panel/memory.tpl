{% extends '@backend/profiler/profiler/layout.tpl' %}

{% block toolbar %}
    {% set icon %}
        {% set status_color = (collector.memory / 1024 / 1024) > 50 ? 'yellow' : '' %}
        {{ include ('@backend/profiler/icon/memory.svg') }}
        <span class="tulia-toolbar-value">{{ '%.1f'|format(collector.memory / 1024 / 1024) }}</span>
        <span class="tulia-toolbar-label">MiB</span>
    {% endset %}

    {% set text %}
        <div class="tulia-toolbar-info-group">
            <div class="tulia-toolbar-info-piece">
                <b>Peak memory usage</b>
                <span>{{ '%.1f'|format(collector.memory / 1024 / 1024) }} MiB</span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>PHP memory limit</b>
                <span>{{ collector.memoryLimit == -1 ? 'Unlimited' : '%.0f MiB'|format(collector.memoryLimit / 1024 / 1024) }}</span>
            </div>
        </div>
    {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false }) }}
{% endblock %}
