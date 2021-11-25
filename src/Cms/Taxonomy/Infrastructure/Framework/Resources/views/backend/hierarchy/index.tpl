{% extends 'backend' %}
{% trans_default_domain 'taxonomy' %}

{% block title %}
    {{ 'hierarchy'|trans({}, 'taxonomy') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomyType: taxonomyType.type }) }}">{{ 'termsListOfTaxonomy'|trans({ taxonomy: taxonomyType.type }) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'hierarchy'|trans }}</li>
{% endblock %}

{% import '@backend/_macros/hierarchy.tpl' as hierarchy %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.term', { taxonomyType: taxonomyType.type }) }}" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> {{ 'cancel'|trans({}, 'messages') }}</a>
                <a href="#" data-submit-form="hierarchy-form" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans({}, 'messages') }}</a>
            </div>
            <i class="pane-header-icon fas fa-sitemap"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            {{ hierarchy.change(tree, path('backend.term.hierarchy.save', { taxonomyType: taxonomyType.type }), 'taxonomy_hierarchy') }}
        </div>
    </div>
{% endblock %}
