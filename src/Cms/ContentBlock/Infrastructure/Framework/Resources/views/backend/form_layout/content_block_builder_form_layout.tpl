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
                pleaseFillThisField: '{{ 'pleaseFillThisField'|trans({}, 'content_block') }}',
                pleaseSelectBlockType: '{{ 'pleaseSelectBlockType'|trans({}, 'content_block') }}',
                addBlock: '{{ 'addBlock'|trans({}, 'content_block') }}',
                removeBlock: '{{ 'removeBlock'|trans({}, 'content_block') }}',
                blockName: '{{ 'blockName'|trans({}, 'content_block') }}',
                createAndConfigure: '{{ 'createAndConfigure'|trans({}, 'content_block') }}',
            },
            block_types: {{ block_types|json_encode|raw }},
            field_name: '{{ full_name }}',
            field_value: {{ value ? value|raw : {} }}
        };
    </script>
{%- endblock content_block_builder_widget %}
