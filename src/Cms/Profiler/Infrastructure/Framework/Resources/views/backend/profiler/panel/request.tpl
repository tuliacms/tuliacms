{% extends '@backend/profiler/profiler/layout.tpl' %}

{% block toolbar %}
    {% import _self as helper %}
    {% set request_handler %}
        {{ helper.set_handler(collector.controller) }}
    {% endset %}

    {% if collector.redirect %}
        {% set redirect_handler %}
            {{ helper.set_handler(collector.redirect.controller, collector.redirect.route, 'GET' != collector.redirect.method ? collector.redirect.method) }}
        {% endset %}
    {% endif %}

    {% if collector.forwardtoken %}
        {% set forward_profile = profile.childByToken(collector.forwardtoken) %}
        {% set forward_handler %}
            {{ helper.set_handler(forward_profile ? forward_profile.collector('request').controller : 'n/a') }}
        {% endset %}
    {% endif %}

    {% set request_status_code_color = (collector.statuscode >= 400) ? 'red' : (collector.statuscode >= 300) ? 'yellow' : 'green' %}

    {% set icon %}
        <span class="tulia-toolbar-status tulia-toolbar-status-{{ request_status_code_color }}">{{ collector.statuscode }}</span>
        {% if collector.route %}
            {% if collector.redirect %}{{ include('@backend/profiler/icon/redirect.svg') }}{% endif %}
            {% if collector.forwardtoken %}{{ include('@backend/profiler/icon/forward.svg') }}{% endif %}
            <span class="tulia-toolbar-label">{{ 'GET' != collector.method ? collector.method }} @</span>
            <span class="tulia-toolbar-value tulia-toolbar-info-piece-additional">{{ collector.route }}</span>
        {% endif %}
    {% endset %}

    {% set text %}
        <div class="tulia-toolbar-info-group">
            <div class="tulia-toolbar-info-piece">
                <b>HTTP status</b>
                <span>{{ collector.statuscode }} {{ collector.statustext }}</span>
            </div>

            {% if 'GET' != collector.method -%}
                <div class="tulia-toolbar-info-piece">
                    <b>Method</b>
                    <span>{{ collector.method }}</span>
                </div>
            {%- endif %}

            <div class="tulia-toolbar-info-piece">
                <b>Controller</b>
                <span>{{ request_handler }}</span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Route name</b>
                <span>{{ collector.route|default('n/a') }}</span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Request locale</b>
                <span>{{ collector.requestAttributes.get('_locale') }}</span>
            </div>

            <div class="tulia-toolbar-info-piece">
                <b>Content locale</b>
                <span>{{ collector.requestAttributes.get('_content_locale') }}</span>
            </div>
        </div>

        {% if redirect_handler is defined -%}
            <div class="tulia-toolbar-info-group">
                <div class="tulia-toolbar-info-piece">
                    <b>
                        <span class="tulia-toolbar-redirection-status tulia-toolbar-status-yellow">{{ collector.redirect.status_code }}</span>
                        Redirect from
                    </b>
                    <span>
                        {{ redirect_handler }}
                        (<a href="{{ path('_profiler', { token: collector.redirect.token }) }}">{{ collector.redirect.token }}</a>)
                    </span>
                </div>
            </div>
        {% endif %}

        {% if forward_handler is defined %}
            <div class="tulia-toolbar-info-group">
                <div class="tulia-toolbar-info-piece">
                    <b>Forwarded to</b>
                    <span>
                        {{ forward_handler }}
                        (<a href="{{ path('_profiler', { token: collector.forwardtoken }) }}">{{ collector.forwardtoken }}</a>)
                    </span>
                </div>
            </div>
        {% endif %}
    {% endset %}

    {{ include('@backend/profiler/profiler/toolbar_item.tpl', { link: false }) }}
{% endblock %}

{% macro set_handler(controller, route, method) %}
    {% if controller.class is defined -%}
        {%- if method|default(false) %}<span class="tulia-toolbar-status tulia-toolbar-redirection-method">{{ method }}</span>{% endif -%}
        {%- set link = controller.file|file_link(controller.line) %}
        {%- if link %}<a href="{{ link }}" title="{{ controller.class }}">{% else %}<span title="{{ controller.class }}">{% endif %}

        {%- if route|default(false) -%}
            @{{ route }}
        {%- else -%}
            {{- controller.class|abbr_class|striptags -}}
            {{- controller.method ? '::' ~ controller.method -}}
        {%- endif -%}

        {%- if link %}</a>{% else %}</span>{% endif %}
    {%- else -%}
        <span>{{ route|default(controller) }}</span>
    {%- endif %}
{% endmacro %}
