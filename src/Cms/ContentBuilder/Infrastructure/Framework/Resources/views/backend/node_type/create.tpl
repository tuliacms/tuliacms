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
    <div id="content-builder-layout-builder"></div>

    <script nonce="{{ csp_nonce() }}">
        window.ContentBuilderLayoutBuilder = {
            translations: {
                createNodeType: '{{ 'createNodeType'|trans }}',
                yes: '{{ 'yes'|trans({}, 'messages') }}',
                no: '{{ 'no'|trans({}, 'messages') }}',
                close: '{{ 'close'|trans({}, 'messages') }}',
                cancel: '{{ 'cancel'|trans({}, 'messages') }}',
                create: '{{ 'create'|trans({}, 'messages') }}',
                icon: '{{ 'icon'|trans({}, 'messages') }}',
                save: '{{ 'save'|trans({}, 'messages') }}',
                nodeTypeName: '{{ 'nodeTypeName'|trans }}',
                nodeTypeNameInfo: '{{ 'nodeTypeNameInfo'|trans }}',
                nodeTypeCode: '{{ 'nodeTypeCode'|trans }}',
                nodeTypeCodeHelp: '{{ 'nodeTypeCodeHelp'|trans }}',
                editNodeTypeDetails: '{{ 'editNodeTypeDetails'|trans }}',
                taxonomyField: '{{ 'taxonomyField'|trans }}',
                taxonomyFieldHelp: '{{ 'taxonomyFieldHelp'|trans }}',
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
                routableType: '{{ 'routableType'|trans }}',
                routableTypeHelp: '{{ 'routableTypeHelp'|trans }}',
                hierarchicalType: '{{ 'hierarchicalType'|trans }}',
                hierarchicalTypeHelp: '{{ 'hierarchicalTypeHelp'|trans }}',
                pleaseSelectValue: '{{ 'pleaseSelectValue'|trans }}',
            },
            fieldTypes: {{ fieldTypes|json_encode|raw }},
            model: {{ model|raw }},
            listingUrl: '{{ path('backend.content_builder.homepage') }}',
            csrfToken: '{{ csrf_token('create-node-type') }}',
        };
    </script>
{% endblock %}
