{% extends 'backend' %}

{% if taxonomyType.isRoutable %}
    {% set previewLink = term_path(term) %}
{% endif %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block title %}
    {{ 'editTerm'|trans({}, taxonomyType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomyType: taxonomyType.type }) }}">{{ 'terms'|trans({}, taxonomyType.translationDomain) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'edit'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(formDescriptor.formView.cancel) }}
                {{ form_row(formDescriptor.formView.save) }}
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {{ alerts.translation_missing_info(term.translated) }}
            {{ render_content_builder_form_layout(formDescriptor) }}
        </div>
    </div>
{% endblock %}
