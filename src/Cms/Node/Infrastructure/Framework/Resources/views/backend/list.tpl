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
        {#{% if nodes is not empty %}
            <table class="table pages-list">
                <tbody>
                {% for node in nodes %}
                    <tr data-element-name="{{ node.title }}" data-element-id="{{ node.id }}">
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
        {% endif %}#}
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
