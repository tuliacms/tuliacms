{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}
{% assets ['content_builder.layout_builder'] %}

{% block title %}
    {{ 'createNodeType'|trans({}, 'content_builder') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.content_builder.homepage') }}">{{ 'contentModel'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createNodeType'|trans({}, 'content_builder') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">

            </div>
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            <div id="content-builder-layout-builder"></div>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        window.ContentBuilderLayoutBuilder = {
            translations: {
                cancel: '{{ 'cancel'|trans({}, 'messages') }}',
                create: '{{ 'create'|trans({}, 'messages') }}',
                title: '{{ 'title'|trans({}, 'messages') }}',
                slug: '{{ 'slug'|trans({}, 'messages') }}',
                addNewSection: '{{ 'addNewSection'|trans }}',
                addNewField: '{{ 'addNewField'|trans }}',
                fieldLabel: '{{ 'fieldLabel'|trans }}',
                fieldId: '{{ 'fieldId'|trans }}',
                fieldIdHelp: '{{ 'fieldIdHelp'|trans }}',
                youCannotCreateTwoFieldsWithTheSameId: '{{ 'youCannotCreateTwoFieldsWithTheSameId'|trans }}',
            }
        };
    </script>
{% endblock %}
