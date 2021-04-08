{% extends '@backend/user/me/layout/base.tpl' %}

{% set activeTab = 'personalization' %}

{% block title %}
    {{ 'personalization'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'personalization'|trans }}</li>
{% endblock %}

{% block mainContent %}
    <div class="card">
        <div class="card-body">
            Personalization options will be available soon.
        </div>
    </div>
{% endblock %}
