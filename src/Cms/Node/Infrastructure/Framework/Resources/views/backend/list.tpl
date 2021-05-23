{% extends 'backend' %}

{% block title %}
    {{ 'nodesList'|trans({}, nodeType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'nodes'|trans({}, nodeType.translationDomain) }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <div class="dropdown">
                    <button class="btn btn-secondary btn-icon-only" type="button" data-toggle="dropdown">
                        <i class="btn-icon fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">{{ 'goTo'|trans }}</h6>
                        <div class="dropdown-divider"></div>
                        {% for tax in taxonomies %}
                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.term', { taxonomyType: tax.type }) }}"><i class="dropdown-icon fas fa-tags"></i> {{ 'taxonomy'|trans({}, tax.translationDomain) }}</a>
                        {% endfor %}
                        <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.settings', { group: 'node.' ~ nodeType.type }) }}"><i class="dropdown-icon fas fa-cogs"></i> {{ 'settings'|trans }}</a>
                    </div>
                </div>
                <a href="{{ path('backend.node.create', { node_type: nodeType.type }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.node.datatable', { node_type: nodeType.type }),
            pagination: false
        }) }}
        {#<div class="pane-body">
            <div class="pages-list-header">
                <div class="action-group">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            {{ 'selected'|trans }}
                        </button>
                        <div class="dropdown-menu">
                            {% if criteria.node_status == 'trashed' %}
                                <a class="dropdown-item dropdown-item-with-icon action-element-selected" href="#" data-action="publish" title="{{ 'publishSelected'|trans }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'publishSelected'|trans }}</a>
                                <a class="dropdown-item dropdown-item-with-icon dropdown-item-danger action-element-selected" href="#" data-action="delete" title="{{ 'deleteSelected'|trans }}"><i class="dropdown-icon fas fa-times"></i> {{ 'deleteSelected'|trans }}</a>
                            {% endif %}
                            {% if criteria.node_status == 'published' %}
                                <a class="dropdown-item dropdown-item-with-icon action-element-selected" href="#" data-action="trash" title="{{ 'trashSelected'|trans }}"><i class="dropdown-icon fas fa-trash"></i> {{ 'trashSelected'|trans }}</a>
                            {% endif %}
                            {% if criteria.node_status == 'sketch' %}
                                <a class="dropdown-item dropdown-item-with-icon action-element-selected" href="#" data-action="publish" title="{{ 'publishSelected'|trans }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'publishSelected'|trans }}</a>
                                <a class="dropdown-item dropdown-item-with-icon action-element-selected" href="#" data-action="trash" title="{{ 'trashSelected'|trans }}"><i class="dropdown-icon fas fa-trash"></i> {{ 'trashSelected'|trans }}</a>
                            {% endif %}
                        </div>
                    </div>
                </div>
                <div class="action-group">
                    <form action="" method="get" class="quick-search" autocomplete="off">
                        <input type="hidden" name="node_type" value="{{ criteria.node_type }}" />
                        <input type="hidden" name="node_status" value="{{ criteria.node_status }}" />
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="{{ 'searchPlaceholder'|trans }}" value="{{ app.request.query.get('q') }}" />
                            {% if app.request.query.get('q') %}
                                <div class="input-group-append">
                                    <a href="{{ path('backend.node', { node_type: criteria.node_type }) }}" class="btn btn-icon-only btn-primary" data-toggle="tooltip" title="{{ 'clearSearch'|trans }}"><i class="btn-icon fas fa-times"></i></a>
                                </div>
                            {% endif %}
                            <div class="input-group-append">
                                <button class="btn btn-icon-only btn-primary" type="submit"><i class="btn-icon fas fa-search"></i></button>
                            </div>
                            <div class="input-group-append">
                                <button class="btn btn-icon-only btn-primary btn-advanced-search" type="button" data-toggle="tooltip" title="{{ 'advancedSearch'|trans }}"><i class="btn-icon fas fa-chevron-down"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="action-group">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                            {% if criteria.node_status == 'published' %}
                                {{ 'showOfType'|trans({ type: 'published'|trans }) }}
                            {% elseif criteria.node_status == 'sketch' %}
                                {{ 'showOfType'|trans({ type: 'sketch'|trans }) }}
                            {% elseif criteria.node_status == 'trashed' %}
                                {{ 'showOfType'|trans({ type: 'trashed'|trans }) }}
                            {% else %}
                                {{ 'showAll'|trans }}
                            {% endif %}
                        </button>
                        {% set currentQuery = app.request.query %}
                        <div class="dropdown-menu">
                            <a class="dropdown-item dropdown-item-with-icon{{ criteria.node_status is empty ? ' active' : '' }}" href="{{ path('backend.node', currentQuery|merge({ node_type: criteria.node_type, node_status: '' })) }}"><i class="dropdown-icon fas fa-dot-circle"></i> {{ 'all'|trans }}</a>
                            <a class="dropdown-item dropdown-item-with-icon{{ criteria.node_status == 'published' ? ' active' : '' }}" href="{{ path('backend.node', currentQuery|merge({ node_type: criteria.node_type, node_status: 'published' })) }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'published'|trans }}</a>
                            <a class="dropdown-item dropdown-item-with-icon{{ criteria.node_status == 'sketch' ? ' active' : '' }}" href="{{ path('backend.node', currentQuery|merge({ node_type: criteria.node_type, node_status: 'sketch' })) }}"><i class="dropdown-icon fas fa-pen-alt"></i> {{ 'sketch'|trans }}</a>
                            <a class="dropdown-item dropdown-item-with-icon{{ criteria.node_status == 'trashed' ? ' active' : '' }}" href="{{ path('backend.node', currentQuery|merge({ node_type: criteria.node_type, node_status: 'trashed' })) }}"><i class="dropdown-icon fas fa-trash"></i> {{ 'trash'|trans }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {% if nodes is not empty %}
            <table class="table pages-list">
                <thead>
                <tr>
                    <th class="col-checkbox">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="node-row-checkbox-select-all" data-select-all=".node-row-checkbox">
                            <label class="custom-control-label" for="node-row-checkbox-select-all"></label>
                        </div>
                    </th>
                    <th class="text-center col-uuid">ID</th>
                    <th class="">{{ 'title'|trans }}</th>
                    <th class="text-center">{{ 'category'|trans }}</th>
                    <th class="text-center">{{ 'status'|trans }}</th>
                    <th class="col-date">{{ 'date'|trans }}</th>
                    <th class="col-actions">{{ 'actions'|trans }}</th>
                </tr>
                </thead>
                <tbody>
                {% for node in nodes %}
                    <tr data-element-name="{{ node.title }}" data-element-id="{{ node.id }}">
                        <td class="col-checkbox">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input action-element-checkbox node-row-checkbox" id="node-row-checkbox-{{ node.id }}" value="{{ node.id }}">
                                <label class="custom-control-label" for="node-row-checkbox-{{ node.id }}"></label>
                            </div>
                        </td>
                        <td data-label="ID" class="text-center col-uuid">
                            <span class="short-uuid-shower" data-copy-dblclick="{{ node.id }}" data-toggle="tooltip" title="Kopiuj -> dwuklik">{{ node.id|shorten_uuid }}</span>
                        </td>
                        <td data-label="{{ 'title'|trans }}" class="col-title">
                            <a href="{{ path('backend.node.edit', { node_type: node.type, id: node.id }) }}" class="link-title" title="{{ node.title }}">
                                <span class="boxur-depth boxur-depth-{{ node.level }}">
                                    {% if node.autogeneratedLocale %}
                                        <span class="badge badge-info" data-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
                                    {% endif %}
                                    {% if criteria.node_status is empty %}
                                        {% if node.status == 'sketch' %}
                                            <span class="badge badge-secondary"><i class="dropdown-icon fas fa-pen-alt"></i> &nbsp;{{ 'sketch'|trans }}</span>
                                        {% elseif node.status == 'trashed' %}
                                            <span class="badge badge-warning"><i class="dropdown-icon fas fa-trash"></i> &nbsp;{{ 'trashed'|trans }}</span>
                                        {% endif %}
                                    {% endif %}
                                    <span class="node-title">{{ node.title }}</span>
                                    <br />
                                    <span class="slug">{{ 'slugValue'|trans({ slug: node.slug }) }}</span>
                                </span>
                            </a>
                        </td>
                        <td data-label="{{ 'category'|trans }}" class="text-center">
                            {% if node.category and node.__category_name %}
                                <a href="{{ path('backend.node', {
                                    taxonomy: 'category',
                                    taxonomy_term: node.category
                                }) }}">{{ node.__category_name }}</a>
                            {% endif %}
                        </td>
                        <td data-label="{{ 'status'|trans }}" class="text-center">
                            <small>
                                {% if node.status == 'sketch' %}
                                    <span class="text-secondary">{{ node.status|trans }}</span>
                                {% elseif node.status == 'trashed' %}
                                    <span class="text-warning">{{ node.status|trans }}</span>
                                {% else %}
                                    <span class="text-success">{{ node.status|trans }}</span>
                                {% endif %}
                            </small>
                        </td>
                        <td data-label="{{ 'date'|trans }}" class="col-date">
                            <span title="{{ node.publishedAt.format('Y-m-d H:i:s') }}">
                                {% if node.publishedAt > date() %}
                                    {{ 'plannedOnDate'|trans({ date: node.publishedAt.format('Y-m-d H:i:s') }) }}
                                {% else %}
                                    {{ node.publishedAt.format('Y-m-d') }}
                                {% endif %}
                            </span>
                        </td>
                        <td data-label="{{ 'actions'|trans }}" class="col-actions">
                            <div class="actions-box">
                                <a href="#" class="btn btn-secondary btn-icon-only" data-toggle="tooltip" title="Szybka edycja"><i class="btn-icon fas fa-map"></i></a>
                                <div class="btn-group">
                                    <a href="{{ path('backend.node.edit', { node_type: node.type, id: node.id }) }}" class="btn btn-secondary btn-icon-only btn-main-action">
                                        <i class="btn-icon fas fa-pen"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <h6 class="dropdown-header">{{ 'moreOptions'|trans }}</h6>
                                            <div class="dropdown-divider"></div>
                                            {% if node.status == 'published' %}
                                                {% set path = node_path(node) %}
                                                {% if path %}
                                                    <a class="dropdown-item dropdown-item-with-icon action-element-single-auto-form" href="{{ path }}" target="_blank" title="{{ 'previewNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'previewNode'|trans({}, nodeType.translationDomain) }}</a>
                                                {% endif %}
                                            {% endif %}
                                            {% if node.status == 'trashed' %}
                                                <a class="dropdown-item dropdown-item-with-icon action-element-single" href="#" data-action="publish" title="{{ 'publishNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-check"></i> {{ 'publishNode'|trans({}, nodeType.translationDomain) }}</a>
                                                <a class="dropdown-item dropdown-item-with-icon dropdown-item-danger action-element-single" href="#" data-action="delete" title="{{ 'deleteNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-times"></i> {{ 'deleteNode'|trans({}, nodeType.translationDomain) }}</a>
                                            {% endif %}
                                            {% if node.status == 'published' %}
                                                <a class="dropdown-item dropdown-item-with-icon action-element-single" href="#" data-action="trash" title="{{ 'trashNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-trash"></i> {{ 'trashNode'|trans({}, nodeType.translationDomain) }}</a>
                                            {% endif %}
                                            {% if node.status == 'sketch' %}
                                                <a class="dropdown-item dropdown-item-with-icon action-element-single" href="#" data-action="publish" title="{{ 'publishNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-check"></i> {{ 'publishNode'|trans({}, nodeType.translationDomain) }}</a>
                                                <a class="dropdown-item dropdown-item-with-icon action-element-single" href="#" data-action="trash" title="{{ 'trashNode'|trans({}, nodeType.translationDomain) }}"><i class="dropdown-icon fas fa-trash"></i> {{ 'trashNode'|trans({}, nodeType.translationDomain) }}</a>
                                            {% endif %}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                {% endfor %}
                </tbody>
            </table>
        {% else %}
            <div class="table-empty">
                <p>{{ 'emptyTableListAddSomethingToStart'|trans }}</p>
                <a href="{{ path('backend.node.create', { node_type: criteria.node_type }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
        {% endif %}
        <div class="pane-footer">
            <div class="pages-list-bottom">
                <div class="actions-box">

                </div>
                <div class="pagination-box">
                    {{ paginator.position('right')|raw }}
                </div>
            </div>
        </div>#}
    </div>
    {#<script nonce="{{ csp_nonce() }}">
        $(function () {
            new Tulia.ElementsActions({
                actions: {
                    trash: {
                        headline: '{{ 'trashSelectedNodes'|trans({}, nodeType.translationDomain) }}',
                        question: '{{ 'areYouSureYouWantToTrashFollowingNodes'|trans({}, nodeType.translationDomain) }}',
                        action: '{{ path('backend.node.change_status', { status: 'trashed', _token: csrf_token('node.change-status') }) }}',
                        confirmation: false,
                    },
                    publish: {
                        headline: '{{ 'publishSelectedNodes'|trans({}, nodeType.translationDomain) }}',
                        question: '{{ 'areYouSureYouWantToPublishFollowingNodes'|trans({}, nodeType.translationDomain) }}',
                        action: '{{ path('backend.node.change_status', { status: 'published', _token: csrf_token('node.change-status') }) }}',
                    },
                    delete: {
                        headline: '{{ 'deleteSelectedNodes'|trans({}, nodeType.translationDomain) }}',
                        question: '{{ 'areYouSureYouWantToDeleteFollowingNodes'|trans({}, nodeType.translationDomain) }}',
                        action: '{{ path('backend.node.delete', { _token: csrf_token('node.delete') }) }}',
                    },
                }
            });

            let query = '{{ app.request.query.get('q') }}';

            if (query) {
                $('.node-title').each(function () {
                    let title = $(this).text();
                    let position = title.toLowerCase().indexOf(query.toLowerCase());

                    if (position >= 0) {
                        let positionEnd = position + query.length;

                        title = [title.slice(0, positionEnd), '</span>', title.slice(positionEnd)].join('');
                        title = [title.slice(0, position), '<span class="text-highlight">', title.slice(position)].join('');

                        $(this).html(title);
                    }
                });
            }
        });
    </script>#}
{% endblock %}
