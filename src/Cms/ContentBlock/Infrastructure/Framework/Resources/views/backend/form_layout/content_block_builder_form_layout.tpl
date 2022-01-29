{% use '@form/cms_form_layout.tpl' %}

{% block content_block_builder_widget -%}
    {% assets ['content_block_builder'] %}
    <div id="content-block-builder"></div>
    <script nonce="{{ csp_nonce() }}">
        window.ContentBlockBuilder = {
            translations: {
                yes: '{{ 'yes'|trans({}, 'messages') }}',
                no: '{{ 'no'|trans({}, 'messages') }}',
                close: '{{ 'close'|trans({}, 'messages') }}',
                cancel: '{{ 'cancel'|trans({}, 'messages') }}',
                create: '{{ 'create'|trans({}, 'messages') }}',
                save: '{{ 'save'|trans({}, 'messages') }}',
                pleaseFillThisField: '{{ 'pleaseFillThisField'|trans({}, 'content_block') }}',
                pleaseSelectBlockType: '{{ 'pleaseSelectBlockType'|trans({}, 'content_block') }}',
                addBlock: '{{ 'addBlock'|trans({}, 'content_block') }}',
                removeBlock: '{{ 'removeBlock'|trans({}, 'content_block') }}',
                editBlock: '{{ 'editBlock'|trans({}, 'content_block') }}',
                blockName: '{{ 'blockName'|trans({}, 'content_block') }}',
                createAndConfigure: '{{ 'createAndConfigure'|trans({}, 'content_block') }}',
                loading: '{{ 'loading'|trans({}, 'content_block') }}',
                missingBlockType: '{{ 'missingBlockType'|trans({}, 'content_block') }}',
                cannotEditBlockWhenContentTypeNotExists: '{{ 'cannotEditBlockWhenContentTypeNotExists'|trans({}, 'content_block') }}',
                cannotEditThisBlock: '{{ 'cannotEditThisBlock'|trans({}, 'content_block') }}',
            },
            block_types: {{ block_types|json_encode|raw }},
            field_name: '{{ full_name }}',
            field_value: '{{ value|raw }}',
            field_name_pattern: 'content_builder_form_%block_type%[%field%]',
            cors_domain: '{{ app.request.schemeAndHttpHost }}',
        };
    </script>
{%- endblock content_block_builder_widget %}
