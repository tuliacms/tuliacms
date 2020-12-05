{% extends 'backend' %}

{% set activeTab = activeTab|default('my-account') %}

{% block title %}
    {{ 'myAccount'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'myAccount'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-user-tie"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="my-account">
                {% include relative(_self, '../parts/sidebar.tpl') %}
                <div class="main-content">
                    {% include relative(_self, '../parts/tabs.tpl') %}
                    {% block mainContent %}
                        main content
                    {% endblock %}
                </div>
            </div>
        </div>
    </div>
    <style>
        {% include relative(_self, '../parts/style.css') %}
    </style>
{% endblock %}
