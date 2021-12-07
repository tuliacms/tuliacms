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
                save: '{{ 'save'|trans({}, 'messages') }}',
                nodeTypeName: '{{ 'nodeTypeName'|trans }}',
                editNodeTypeDetails: '{{ 'editNodeTypeDetails'|trans }}',
                slug: '{{ 'slug'|trans({}, 'messages') }}',
                addNewSection: '{{ 'addNewSection'|trans }}',
                addNewField: '{{ 'addNewField'|trans }}',
                fieldLabel: '{{ 'fieldLabel'|trans }}',
                fieldLabelHelp: '{{ 'fieldLabelHelp'|trans }}',
                fieldId: '{{ 'fieldId'|trans }}',
                fieldIdHelp: '{{ 'fieldIdHelp'|trans }}',
                youCannotCreateTwoFieldsWithTheSameId: '{{ 'youCannotCreateTwoFieldsWithTheSameId'|trans }}',
                theseOptionsWillNotBeEditableAfterSave: '{{ 'theseOptionsWillNotBeEditableAfterSave'|trans }}',
                editField: '{{ 'editField'|trans }}',
                fieldType: '{{ 'fieldType'|trans }}',
                removeField: '{{ 'removeField'|trans }}',
                removeSection: '{{ 'removeSection'|trans }}',
                multilingualField: '{{ 'multilingualField'|trans }}',
                multilingualFieldInfo: '{{ 'multilingualFieldInfo'|trans }}',
                nextStep: '{{ 'nextStep'|trans }}',
                pleaseFillThisField: '{{ 'pleaseFillThisField'|trans }}',
                fieldIdMustContainOnlyAlphanumsAndUnderline: '{{ 'fieldIdMustContainOnlyAlphanumsAndUnderline'|trans }}',
                fieldTypeConfiguration: '{{ 'fieldTypeConfiguration'|trans }}',
                fieldDetails: '{{ 'fieldDetails'|trans }}',
                fieldTypeConstraints: '{{ 'fieldTypeConstraints'|trans }}',
                thisFieldDoesNotHaveConfiguration: '{{ 'thisFieldDoesNotHaveConfiguration'|trans }}',
            },
            fieldTypes: {{ fieldTypes|json_encode|raw }}
        };
    </script>
{% endblock %}
