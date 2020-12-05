{% extends '@backend/profiler/profiler/layout.tpl' %}

{% block toolbar %}
    {% set icon %}
        {{ include('@backend/profiler/icon/translation.svg') }}
        {% set status_color = collector.countMissings ? 'red' : collector.countFallbacks ? 'yellow' %}
        {% set error_count = collector.countMissings + collector.countFallbacks %}
        <span class="tulia-toolbar-value">{{ error_count ?: collector.countDefines }}</span>
    {% endset %}

    {% set text %}
        <div class="tulia-toolbar-info-group">
            <div class="tulia-toolbar-info-piece">
                <b>Locale</b>
                <span class="tulia-toolbar-status">
                    {{ collector.locale|default('-') }}
                </span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Missing messages</b>
                <span class="tulia-toolbar-status tulia-toolbar-status-{{ collector.countMissings ? 'red' }}">
                    {{ collector.countMissings }}
                </span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Fallback messages</b>
                <span class="tulia-toolbar-status tulia-toolbar-status-{{ collector.countFallbacks ? 'yellow' }}">
                    {{ collector.countFallbacks }}
                </span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Defined messages</b>
                <span class="tulia-toolbar-status">{{ collector.countDefines }}</span>
            </div>
        </div>
    {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false, status: status_color }) }}
{% endblock %}
