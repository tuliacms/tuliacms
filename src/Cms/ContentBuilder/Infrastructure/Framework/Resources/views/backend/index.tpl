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
                <a href="{{ path('backend.content_builder.content_type.edit', { id: type.id, contentType: type.type }) }}">
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
                <a href="{{ path('backend.content_builder.content_type.edit', { id: type.id, contentType: type.type }) }}" class="card-link py-3 d-inline-block" title="{{ 'edit'|trans({}, 'messages') }}">{{ 'edit'|trans({}, 'messages') }}</a>
                <a href="#" class="card-link"></a>
                <div class="dropup d-inline-block float-right">
                    <a href="#" class="card-link d-inline-block px-4 py-3 text-dark" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu">
                        <a href="#" data-href="{{ path('backend.content_builder.content_type.delete', { id: type.id, contentType: type.type }) }}" class="dropdown-item dropdown-item-danger dropdown-item-with-icon content-type-delete-trigger" title="{{ 'delete'|trans({}, 'messages') }}" data-id="{{ type.code }}"><i class="dropdown-icon fas fa-times"></i>{{ 'delete'|trans({}, 'messages') }}</a>
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
        <div class="pane pane-lead mb-4">
            <div class="pane-header">
                {% if loop.index0 == 0 %}
                    <div class="pane-buttons">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#content-model-import-modal" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-cloud-upload-alt"></i> {{ 'import'|trans({}, 'messages') }}</a>
                    </div>
                {% endif %}
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
                            <a href="{{ path('backend.content_builder.content_type.create', { contentType: type }) }}" class="content-type-create-button">
                                <div class="content-type-create-button-inner">
                                    <i class="fas fa-plus"></i>
                                    {{ 'createContentTypeOf'|trans({ name: type|trans }) }}
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {% endfor %}

    <div class="modal fade" id="content-model-import-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ 'import'|trans({}, 'messages') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ path('backend.content_builder.import.file') }}" method="POST" enctype="multipart/form-data" id="submit-content-types-import">
                        <input type="hidden" name="_token" value="{{ csrf_token('content-builder-import-file') }}" />
                        <div class="mb-3">
                            <label for="importing-file" class="form-label">Select field</label>
                            <input class="form-control" name="file" type="file" id="importing-file" />
                        </div>
                        <div class="alert alert-info">
                            {{ 'importingOverwriteNotification'|trans }}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans({}, 'messages') }}</button>
                    <button type="button" class="btn btn-success" data-submit-form="submit-content-types-import">{{ 'doImport'|trans({}, 'messages') }}</button>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" id="content-type-remove-form" style="display:none">
        <input type="text" name="_token" value="{{ csrf_token('delete-content-type') }}" />
    </form>

    <style>
        .content-type-create-button {
            min-height: 210px;
            display: flex;
            align-items: center;
            align-content: center;
        }
        .content-type-create-button .content-type-create-button-inner {
            text-align: center;
            font-size: 15px;
            flex: 1 1 100%;
        }
        .content-type-create-button .fas {
            display: block;
            font-size: 60px;
            color: #ccc;
            margin-bottom: 20px;
            transition: .12s all;
        }
        .content-type-create-button:hover {
            text-decoration: none;
        }
        .content-type-create-button:hover .fas {
            color: #aaa;
        }
    </style>
    <script>
        $(function () {
            $('.content-type-delete-trigger').click(function (e) {
                e.preventDefault();
                let action = $(this).attr('data-href');

                Tulia.Confirmation
                    .warning({
                        title: 'You want to remove this Content Type?',
                        text: 'Removeing Content type won\'t remove contents, first remove all the contents from this type.',
                    })
                    .then(function (v) {
                        if (! v.value) {
                            return;
                        }

                        Tulia.PageLoader.show();
                        $('#content-type-remove-form').attr('action', action).submit();
                    });
            });
        });
    </script>
{% endblock %}
