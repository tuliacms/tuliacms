{% extends 'backend' %}

{% block title %}
    {{ 'changeHierarchy'|trans({}, 'menu') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.menu') }}">{{ 'menuList'|trans }}</a></li>
    <li class="breadcrumb-item"><a href="{{ path('backend.menu.item', { menuId: menuId }) }}">{{ menu.name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'changeHierarchy'|trans({}, 'menu') }}</li>
{% endblock %}

{% import '@backend/_macros/hierarchy.tpl' as hierarchy %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.menu.item', { menuId: menuId }) }}" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> {{ 'cancel'|trans }}</a>
                <a href="#" data-submit-form="hierarchy-form" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-sitemap"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            {{ hierarchy.change(tree, path('backend.menu.item.hierarchy.save', { menuId: menuId }), 'menu_hierarchy') }}
        </div>
    </div>
{% endblock %}
