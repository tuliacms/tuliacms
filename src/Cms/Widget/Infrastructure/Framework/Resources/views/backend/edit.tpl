{% extends 'backend' %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editWidget'|trans({}, 'widgets') }} - {{ widgetInfo.name|default('_name not provided_')|trans({}, widgetInfo.translationDomain|default('widgets')) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.widget') }}">{{ 'widgets'|trans({}, 'widgets') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editWidget'|trans({}, 'widgets') }} - {{ widgetInfo.name|default('_name not provided_')|trans({}, widgetInfo.translationDomain|default('widgets')) }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(formDescriptor.formView.cancel) }}
                {{ form_row(formDescriptor.formView.save) }}
            </div>
            <i class="pane-header-icon fas fa-window-restore"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(widget.translated) }}
            {{ render_content_builder_form_layout(formDescriptor) }}
        </div>
    </div>
{% endblock %}
