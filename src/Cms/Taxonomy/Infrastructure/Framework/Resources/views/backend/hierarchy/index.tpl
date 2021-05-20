{% extends 'backend' %}

{% assets ['nestable'] %}

{% block title %}
    {{ 'hierarchy'|trans({}, 'taxonomy') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.term', { taxonomy_type: taxonomyType.type }) }}">{{ 'terms'|trans({}, taxonomyType.translationDomain) }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'hierarchy'|trans({}, 'taxonomy') }}</li>
{% endblock %}

{% set nestableId = 'taxonomy-nestable-' ~ uniqid() %}

{% macro treeLeaf(items) %}
    {% for item in items %}
        <li class="dd-item" data-id="{{ item.id }}">
            <div class="dd-handle"><i class="fa fa-arrows-alt dd-handle-icon"></i> {{ item.name }}</div>
            {% if item.children %}
                <ol class="dd-list">
                    {{ _self.treeLeaf(item.children) }}
                </ol>
            {% endif %}
        </li>
    {% endfor %}
{% endmacro %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> {{ 'cancel'|trans }}</a>
                <a href="#" data-submit-form="hierarchy-form" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-sitemap"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <form action="{{ path('backend.term.hierarchy.save', { taxonomy_type: taxonomyType.type }) }}" id="hierarchy-form" method="post">
                <input type="hidden" value="{{ csrf_token('taxonomy_hierarchy') }}" name="_token" />
            </form>

            <div class="alert alert-primary" role="alert">
                <h4 class="alert-heading">{{ 'changeHierarchy'|trans({}, 'taxonomy') }}</h4>
                <p class="mb-0">{{ 'changeHierarchyMoveElementOnTheThree'|trans({}, 'taxonomy') }}</p>
            </div>

            <div class="dd nestable2" id="{{ nestableId }}">
                <ol class="dd-list">
                    {{ _self.treeLeaf(tree) }}
                </ol>
            </div>
        </div>
    </div>
    <style>
        body .dd {max-width:100%}
        body .dd-handle {padding:10px 15px 9px;height:auto;font-size:14px;font-weight:normal;transition:.12s all;}
        body .dd-handle .dd-handle-icon {display:inline-block;margin-right:5px;}
        body .dd-handle:hover {cursor:move;}
        body .dd-empty,
        body .dd-item,
        body .dd-placeholder {line-height:1.2}
        body .dd-item > button {height:27px;width:30px;font-size:16px}
        body .dd-item > button.dd-collapse:before {content:"\f107";font-family:"Font Awesome 5 Free",serif;font-weight:900}
        body .dd-item > button.dd-expand:before {content:"\f106";font-family:"Font Awesome 5 Free",serif;font-weight:900}
    </style>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            let form = $('#hierarchy-form');
            let nest = $('.dd#{{ nestableId }}');

            nest.nestable({
                callback: function() {
                    let data = nest.nestable('toArray');
                    form.find('input.terms-hierarchy').remove();

                    for (let i = 0; i < data.length; i++) {
                        form.append('<input type="text" class="terms-hierarchy" name="term[' + data[i].id + ']" value="' + (data[i].parent_id || 0) + '" />');
                    }
                }
            });
        });
    </script>
{% endblock %}
