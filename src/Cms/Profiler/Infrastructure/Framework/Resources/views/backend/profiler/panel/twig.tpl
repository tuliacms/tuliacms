{% extends '@backend/profiler/profiler/layout.tpl' %}

{% block toolbar %}
    {% set time = collector.templatecount ? '%0.0f'|format(collector.time) : 'n/a' %}

    {% set icon %}
        {{ include('@backend/profiler/icon/twig.svg') }}
        <span class="tulia-toolbar-value">{{ time }}</span>
        <span class="tulia-toolbar-label">ms</span>
    {% endset %}

    {% set text %}
        <div class="tulia-toolbar-info-group">
            <div class="tulia-toolbar-info-piece">
                <b>Render Time</b>
                <span>{{ time }} ms</span>
            </div>
            <div class="tulia-toolbar-info-piece">
                <b>Template Calls</b>
                <span class="tulia-toolbar-status">{{ collector.templatecount }}</span>
            </div>
            <div class="tulia-toolbar-info-piece">
                <b>Block Calls</b>
                <span class="tulia-toolbar-status">{{ collector.blockcount }}</span>
            </div>
            <div class="tulia-toolbar-info-piece">
                <b>Macro Calls</b>
                <span class="tulia-toolbar-status">{{ collector.macrocount }}</span>
            </div>
        </div>
        <div class="tulia-toolbar-info-group">
            {{ collector.HtmlCallGraph|replace(collector.getTemplatePaths)|replace({'/var/www/html':''})|raw }}
        </div>
    {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false }) }}
{% endblock %}
