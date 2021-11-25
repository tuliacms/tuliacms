{% extends 'backend' %}

{% block title %}
    {{ 'nodesList'|trans({}, nodeType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'nodes'|trans({}, nodeType.translationDomain) }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <div class="dropdown">
                    <button class="btn btn-secondary btn-icon-only" type="button" data-bs-toggle="dropdown">
                        <i class="btn-icon fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-left">
                        <h6 class="dropdown-header">{{ 'goTo'|trans }}</h6>
                        <div class="dropdown-divider"></div>
                        {% for tax in taxonomies %}
                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.term', { taxonomyType: tax.type }) }}"><i class="dropdown-icon fas fa-tags"></i> {{ tax.type }}</a>
                        {% endfor %}
                        <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.settings', { group: 'node.' ~ nodeType.type }) }}"><i class="dropdown-icon fas fa-cogs"></i> {{ 'settings'|trans }}</a>
                    </div>
                </div>
                <a href="{{ path('backend.node.create', { node_type: nodeType.type }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas {{ nodeType.icon }}"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.node.datatable', { node_type: nodeType.type }),
            pagination: false
        }) }}
    </div>
{% endblock %}
