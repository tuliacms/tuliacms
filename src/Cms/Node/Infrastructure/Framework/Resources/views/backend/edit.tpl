{% extends 'backend' %}
{% trans_default_domain 'node' %}

{% if nodeType.isRoutable %}
    {% set previewLink = node_path(node) %}
{% endif %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editNode'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.node', { node_type: nodeType.code }) }}">{{ 'nodesListOfType'|trans({ type: nodeType.name|trans }) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'editNode'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(formDescriptor.formView.cancel) }}
                {{ form_row(formDescriptor.formView.save) }}
            </div>
            <i class="pane-header-icon {{ nodeType.icon }}"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(node.translated) }}
            {{ render_content_builder_form_layout(formDescriptor) }}
        </div>
    </div>
{% endblock %}
