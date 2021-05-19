{% extends 'backend' %}

{#{% if taxonomyType.isRoutable %}
    {% set previewLink = term_path(term) %}
{% endif %}#}

{% block title %}
    {{ 'editTerm'|trans({}, taxonomyType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomy_type: taxonomyType.type }) }}">{{ 'terms'|trans({}, taxonomyType.translationDomain) }}</a></li>
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
            {% include relative(_self, 'parts/form-body.tpl') %}
        </div>
    </div>
{% endblock %}
