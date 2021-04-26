{% extends 'backend' %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'createItem'|trans({}, 'menu') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.menu') }}">{{ 'menuList'|trans }}</a></li>
    <li class="breadcrumb-item"><a href="{{ path('backend.menu.item.list', { menuId: menu.id }) }}">{{ menu.name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createItem'|trans({}, 'menu') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(form.save) }}
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            {{ alerts.foreign_locale_creation_info() }}
            {% set persistMode = 'create' %}
            {% include relative(_self, 'parts/form-body.tpl') %}
        </div>
    </div>
{% endblock %}
