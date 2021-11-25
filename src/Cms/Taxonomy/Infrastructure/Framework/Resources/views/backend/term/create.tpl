{% extends 'backend' %}
{% trans_default_domain 'taxonomy' %}

{% block title %}
    {{ 'createTerm'|trans }}
{% endblock %}

{% import '@backend/_macros/alerts.tpl' as alerts %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomyType: taxonomyType.type }) }}">{{ 'termsListOfTaxonomy'|trans({ taxonomy: taxonomyType.type }) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'create'|trans }}</li>
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
            {{ alerts.foreign_locale_creation_info() }}
            {{ render_content_builder_form_layout(formDescriptor) }}
        </div>
    </div>
{% endblock %}
