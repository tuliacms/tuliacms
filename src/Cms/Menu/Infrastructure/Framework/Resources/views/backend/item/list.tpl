{% extends 'backend' %}

{% block title %}
    {{ menu.name }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.menu') }}">{{ 'menuList'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ menu.name }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.menu.item.create', { menuId: menu.id }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'add'|trans }}</a>
                <a href="{{ path('backend.menu.hierarchy', { menuId: menu.id }) }}" class="btn btn-default btn-icon-left"><i class="btn-icon fa fa-sitemap"></i> {{ 'changeHierarchy'|trans({}, 'menu') }}</a>
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>

        {{ generator.generate(datatable, {
            data_endpoint: path('backend.menu.item.datatable', { menuId: menu.id }),
            pagination: false
        }) }}
    </div>
{% endblock %}
