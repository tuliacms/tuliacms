{% extends 'backend' %}

{% if nodeType.isRoutable %}
    {% set previewLink = node_path(node) %}
{% endif %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editNode'|trans({}, nodeType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.node', { node_type: nodeType.type }) }}">{{ 'nodes'|trans({}, nodeType.translationDomain) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'edit'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(form.cancel) }}
                {{ form_row(form.save) }}
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(node.translated) }}
            {% include relative(_self, 'parts/form-body.tpl') %}
        </div>
    </div>
{% endblock %}
