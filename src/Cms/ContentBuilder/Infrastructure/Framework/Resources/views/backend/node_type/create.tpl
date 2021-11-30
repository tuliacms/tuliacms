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
                title: '{{ 'title'|trans({}, 'messages') }}',
                slug: '{{ 'slug'|trans({}, 'messages') }}',
                addNewSection: '{{ 'addNewSection'|trans }}',
                addNewField: '{{ 'addNewField'|trans }}',
            }
        };
    </script>

    <style>
        .ctb-sections-container:empty {
            padding: 30px;
            border: 3px dashed #ddd;
            border-radius: 6px;
            position: relative;
        }
        .ctb-sections-container:empty:before {
            content: attr(data-label);
            color: rgba(0,0,0,.4);
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .ctb-sortable-fields:empty {
            padding: 30px;
            border: 3px dashed #ddd;
            border-radius: 6px;
            position: relative;
        }
        .ctb-sortable-fields:empty:before {
            content: attr(data-label);
            color: rgba(0,0,0,.4);
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .ctb-sortable-fields {
            display: block;
            list-style: none;
            margin: 0;
        }
        .ctb-sortable-fields > div {
            border-radius: 6px;
            border: 2px dashed #ddd;
            padding: 5px 9px;
            margin-bottom: 5px;
            background-color: #fff;
        }
        .ctb-section-main-tabs-contents {
            border-top: 1px solid #d6dce4;
        }
        .ctb-section-label {
            border-bottom: 1px solid #d6dce4;
            background-color: #f7f7f7;
            padding: 11px 15px;
        }
        .ctb-section-fields-container {
            padding: 15px 15px 0 15px;
        }
        .ctb-section-footer {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #d6dce4;
        }
    </style>
{% endblock %}
