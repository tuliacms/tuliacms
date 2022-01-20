{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}

{% macro badge_yes_no(condition) %}
    {% if condition %}
        <span class="badge badge-info">{{ 'yes'|trans({}, 'messages') }}</span>
    {% else %}
        <span class="badge badge-secondary">{{ 'no'|trans({}, 'messages') }}</span>
    {% endif %}
{% endmacro %}

{% macro content_type_element(type) %}
    {% import _self as macros %}

    <div class="card">
        <div class="card-body">
            {% if type.isInternal %}
                <h4 class="card-title"><i class="{{ type.icon }}"></i> &nbsp; {{ type.name|trans({}, 'node') }}</h4>
            {% else %}
                <a href="{{ path('backend.content_builder.content_type.edit', { code: type.code, contentType: type.type }) }}">
                    <h4 class="card-title"><i class="{{ type.icon }}"></i> &nbsp; {{ type.name|trans({}, 'node') }}</h4>
                </a>
            {% endif %}
            <small class="text-muted">{{ 'contentTypeCode'|trans }}: {{ type.code }}</small>
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
                <a href="{{ path('backend.content_builder.content_type.edit', { code: type.code, contentType: type.type }) }}" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans({}, 'messages') }}">{{ 'edit'|trans({}, 'messages') }}</a>
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
    {% for type in contentTypeCodes %}
        <div class="pane pane-lead">
            <div class="pane-header">
                <i class="pane-header-icon fas fa-box"></i>
                <h1 class="pane-title">{{ 'contentTypesListOf'|trans({ name: type|trans }) }}</h1>
            </div>
            <div class="pane-body">
                <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 content-type-list">
                    {% for contentType in contentTypeList %}
                        {% if contentType.type == type %}
                            <div class="col mb-4">
                                {{ macros.content_type_element(contentType) }}
                            </div>
                        {% endif %}
                    {% endfor %}
                    <div class="col mb-4">
                        <div class="card">
                            <a href="{{ path('backend.content_builder.content_type.create', { contentType: type }) }}">
                                {{ 'createContentTypeOf'|trans({ name: type|trans }) }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}

    <style>
        .content-type-list .list-group-item {
            position: relative;
        }
        .content-type-list .list-group-item .website-locale-flag-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 16px;
        }
    </style>
{% endblock %}
