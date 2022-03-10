{% extends 'backend' %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editItem'|trans({}, 'menu') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.menu') }}">{{ 'menuList'|trans }}</a></li>
    <li class="breadcrumb-item"><a href="{{ path('backend.menu.item.list', { menuId: menu.id }) }}">{{ menu.name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editItem'|trans({}, 'menu') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(formDescriptor.formView.cancel) }}
                {{ form_row(formDescriptor.formView.save) }}
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            {{ alerts.translation_missing_info(item.translated) }}
            {{ render_content_builder_form_layout(formDescriptor) }}
        </div>
    </div>
{% endblock %}
