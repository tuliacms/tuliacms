{% extends '@backend/user/me/layout/base.tpl' %}

{% block title %}
    {{ 'myAccount'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'myAccount'|trans }}</li>
{% endblock %}

{% block mainContent %}
    My account
{% endblock %}
