{% extends 'backend' %}

{% assets ['nestable'] %}

{% block title %}
    {{ 'changeHierarchy'|trans({}, 'menu') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.menu') }}">{{ 'menuList'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'changeHierarchy'|trans({}, 'menu') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {#<a href="#" class="btn btn-default btn-icon-left"><i class="btn-icon fa fa-sitemap"></i> {{ 'changeHierarchy'|trans({}, 'menu') }}</a>#}
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
    </div>
{% endblock %}
