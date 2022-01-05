{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}

{% macro badge_yes_no(condition) %}
    {% if condition %}
        <span class="badge badge-info">{{ 'yes'|trans({}, 'messages') }}</span>
    {% else %}
        <span class="badge badge-secondary">{{ 'no'|trans({}, 'messages') }}</span>
    {% endif %}
{% endmacro %}

{% block title %}
    {{ 'contentModel'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'contentModel'|trans }}</li>
{% endblock %}

{% trans_default_domain 'content_builder' %}
{% import _self as macros %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ 'nodeTypesList'|trans }}</h1>
        </div>
        <div class="pane-body">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 websites-list">
                {% for type in nodeTypeList %}
                    <div class="col mb-4">
                        <div class="card">
                            <div class="card-body">
                                {% if type.isInternal %}
                                    <h4 class="card-title"><i class="{{ type.icon }}"></i> &nbsp; {{ type.name|trans({}, 'node') }}</h4>
                                {% else %}
                                    <a href="{{ path('backend.content_builder.node_type.edit', { code: type.code }) }}">
                                        <h4 class="card-title"><i class="{{ type.icon }}"></i> &nbsp; {{ type.name|trans({}, 'node') }}</h4>
                                    </a>
                                {% endif %}
                                <small class="text-muted">{{ 'nodeTypeCode'|trans }}: {{ type.code }}</small>
                            </div>

                            {% set notInternalFieldsCount = 0 %}
                            {% for field in type.fields %}
                                {% if not field.isInternal %}
                                    {% set notInternalFieldsCount = notInternalFieldsCount + 1 %}
                                {% endif %}
                            {% endfor %}

                            <ul class="list-group list-group-flush">
                                {% if type.isInternal %}
                                    <li class="list-group-item"><i>{{ 'internalContentType'|trans }}</i></li>
                                {% endif %}
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isRoutable'|trans }}: {{ macros.badge_yes_no(type.isRoutable) }}</li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isHierarchical'|trans }}: {{ macros.badge_yes_no(type.isHierarchical) }}</li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'numberOfFields'|trans }}: <span class="badge badge-info">{{ notInternalFieldsCount }}</span></li>
                            </ul>
                            {% if type.isInternal == false %}
                                <div class="card-footer py-0 pr-0">
                                    <a href="{{ path('backend.content_builder.node_type.edit', { code: type.code }) }}" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans({}, 'messages') }}">{{ 'edit'|trans({}, 'messages') }}</a>
                                    <a href="#" class="card-link"></a>
                                    <div class="dropup d-inline-block float-right">
                                        <a href="#" class="card-link d-inline-block px-4 py-3 text-dark" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="#" class="dropdown-item dropdown-item-danger dropdown-item-with-icon website-delete-trigger" title="{{ 'delete'|trans({}, 'messages') }}" data-id="{{ type.code }}"><i class="dropdown-icon fas fa-times"></i>{{ 'delete'|trans({}, 'messages') }}</a>
                                        </div>
                                    </div>
                                </div>
                            {% endif %}
                        </div>
                    </div>
                {% endfor %}
                <div class="col mb-4">
                    <div class="card">
                        <a href="{{ path('backend.content_builder.node_type.create') }}">
                            {{ 'createNodeType'|trans({}, 'content_builder') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pane pane-lead mt-4">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ 'taxonomyTypesList'|trans }}</h1>
        </div>
        <div class="pane-body">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 websites-list">
                {% for type in taxonomyTypeList %}
                    <div class="col mb-4">
                        <div class="card">
                            <div class="card-body">
                                {% if type.isInternal %}
                                    <h4 class="card-title">{{ type.name|trans({}, 'node') }}</h4>
                                {% else %}
                                    <a href="">
                                        <h4 class="card-title">{{ type.name|trans({}, 'node') }}</h4>
                                    </a>
                                {% endif %}
                                <small class="text-muted">{{ 'taxonomyTypeCode'|trans }}: {{ type.code }}</small>
                            </div>
                            <ul class="list-group list-group-flush">
                                {% if type.isInternal %}
                                    <li class="list-group-item"><i>{{ 'internalContentType'|trans }}</i></li>
                                {% endif %}
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isRoutable'|trans }}: {{ macros.badge_yes_no(type.isRoutable) }}</li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'isHierarchical'|trans }}: {{ macros.badge_yes_no(type.isHierarchical) }}</li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">{{ 'numberOfFields'|trans }}: <span class="badge badge-info">{{ type.fields|length }}</span></li>
                            </ul>
                            {% if type.isInternal == false %}
                                <div class="card-footer py-0 pr-0">
                                    <a href="" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans({}, 'messages') }}">{{ 'edit'|trans({}, 'messages') }}</a>
                                    <a href="#" class="card-link"></a>
                                    <div class="dropup d-inline-block float-right">
                                        <a href="#" class="card-link d-inline-block px-4 py-3 text-dark" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="#" class="dropdown-item dropdown-item-danger dropdown-item-with-icon website-delete-trigger" title="{{ 'delete'|trans({}, 'messages') }}" data-id="{{ type.code }}"><i class="dropdown-icon fas fa-times"></i>{{ 'delete'|trans({}, 'messages') }}</a>
                                        </div>
                                    </div>
                                </div>
                            {% endif %}
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>

    <style>
        .websites-list .list-group-item {
            position: relative;
        }
        .websites-list .list-group-item .website-locale-flag-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 16px;
        }
    </style>
{% endblock %}
