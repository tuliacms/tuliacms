{% extends '@backend/user/me/layout/base.tpl' %}

{% set activeTab = 'edit' %}

{% block title %}
    {{ 'editAccount'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editAccount'|trans({}, 'users') }}</li>
{% endblock %}

{% block mainContent %}
    {{ form_start(form) }}
    {{ form_errors(form) }}
    {{ form_row(form._token) }}
    {{ form_row(form.id) }}
    {% set save = form_row(form.save) %}

    {{ form_extension_render(manager, 'default', {
        active_first: ['_FIRST_'],
    }) }}
    <div class="my-5"></div>
    {{ save|raw }}

    {{ form_end(form) }}
{% endblock %}
