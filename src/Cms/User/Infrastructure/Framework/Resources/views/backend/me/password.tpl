{% extends '@backend/user/me/layout/base.tpl' %}

{% set activeTab = 'password' %}

{% block title %}
    {{ 'changePassword'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'changePassword'|trans({}, 'users') }}</li>
{% endblock %}

{% block mainContent %}
    {{ form_start(form) }}

    <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />

    {{ form_errors(form) }}
    {{ form_row(form._token) }}
    <div class="form-controls-terminator">
        {{ form_row(form.password) }}
        <div class="alert alert-info">
            {{ 'autoLogoutAfterPasswordChangeInfo'|trans({}, 'users') }}
        </div>
        {{ form_row(form.save) }}
    </div>
    {{ form_end(form) }}
{% endblock %}
