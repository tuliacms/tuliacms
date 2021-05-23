{% extends 'backend' %}

{% block title %}
    {{ 'terms'|trans({}, taxonomyType.translationDomain) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'terms'|trans({}, taxonomyType.translationDomain) }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <div class="dropdown" title="{{ 'moreOptions'|trans }}" data-toggle="tooltip">
                    <button class="btn btn-secondary btn-icon-only" type="button" data-toggle="dropdown">
                        <i class="btn-icon fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <h6 class="dropdown-header">{{ 'goTo'|trans }}</h6>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.settings', { group: 'taxonomy.' ~ taxonomyType.type }) }}"><i class="dropdown-icon fas fa-cogs"></i> {{ 'settings'|trans }}</a>
                    </div>
                </div>
                <a href="{{ path('backend.term.hierarchy', { taxonomyType: criteria.taxonomy_type }) }}" class="btn btn-secondary btn-icon-only" title="{{ 'hierarchy'|trans({}, 'taxonomy') }}" data-toggle="tooltip"><i class="btn-icon fas fa-sitemap"></i></a>
                <a href="{{ path('backend.term.create', { taxonomyType: criteria.taxonomy_type }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="pages-list-header">
                <div class="action-group">
                    <form action="" method="get" class="quick-search" autocomplete="off">
                        <input type="hidden" name="taxonomy_type" value="{{ criteria.taxonomy_type }}" />
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="{{ 'searchPlaceholder'|trans }}" value="{{ app.request.query.get('q') }}" />
                            {% if app.request.query.get('q') %}
                                <div class="input-group-append">
                                    <a href="{{ path('backend.term', { taxonomyType: criteria.taxonomy_type }) }}" class="btn btn-icon-only btn-primary" data-toggle="tooltip" title="{{ 'clearSearch'|trans }}"><i class="btn-icon fas fa-times"></i></a>
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
            </div>
        </div>

        {% if terms is not empty %}
            <table class="table pages-list">
                <thead>
                <tr>
                    <th class="col-checkbox">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="term-row-checkbox-select-all" data-select-all=".term-row-checkbox">
                            <label class="custom-control-label" for="term-row-checkbox-select-all"></label>
                        </div>
                    </th>
                    <th class="text-center col-uuid">ID</th>
                    <th class="">{{ 'name'|trans }}</th>
                    <th class="col-visibility">{{ 'visibility'|trans }}</th>
                    <th class="col-actions">{{ 'actions'|trans }}</th>
                </tr>
                </thead>
                <tbody>
                {% for term in terms %}
                    <tr data-element-name="{{ term.title }}" data-element-id="{{ term.id }}">
                        <td class="col-checkbox">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input action-element-checkbox term-row-checkbox" id="term-row-checkbox-{{ term.id }}" value="{{ term.id }}">
                                <label class="custom-control-label" for="term-row-checkbox-{{ term.id }}"></label>
                            </div>
                        </td>
                        <td data-label="ID" class="text-center col-uuid">
                            <span class="short-uuid-shower" data-copy-dblclick="{{ term.id }}" data-toggle="tooltip" title="Kopiuj -> dwuklik">{{ term.id|shorten_uuid }}</span>
                        </td>
                        <td data-label="{{ 'title'|trans }}" class="col-title">
                            <a href="{{ path('backend.term.edit', { taxonomyType: term.type, id: term.id }) }}" class="link-title" title="{{ term.title }}">
                                <span class="boxur-depth boxur-depth-{{ term.level - 1 }}">
                                    {% if term.autogeneratedLocale %}
                                        <span class="badge badge-info" data-toggle="tooltip" title="{{ 'missingTranslationInThisLocale'|trans }}"><i class="dropdown-icon fas fa-language"></i></span>
                                    {% endif %}
                                    <span class="term-name">{{ term.name }}</span>
                                    <br />
                                    <span class="slug">{{ 'slugValue'|trans({ slug: term.slug }) }}</span>
                                </span>
                            </a>
                        </td>
                        <td data-label="{{ 'visibility'|trans }}" class="col-visibility">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="term-visibility-switch-{{ term.id }}"{{ term.visibility ? ' checked="checked"' : '' }}>
                                <label class="custom-control-label" for="term-visibility-switch-{{ term.id }}"></label>
                            </div>
                        </td>
                        <td data-label="{{ 'actions'|trans }}" class="col-actions">
                            <div class="actions-box">
                                <a href="#" class="btn btn-secondary btn-icon-only" data-toggle="tooltip" title="Szybka edycja"><i class="btn-icon fas fa-map"></i></a>
                                <div class="btn-group">
                                    <a href="{{ path('backend.term.edit', { taxonomyType: term.type, id: term.id }) }}" class="btn btn-secondary btn-icon-only btn-main-action">
                                        <i class="btn-icon fas fa-pen"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <h6 class="dropdown-header">{{ 'moreOptions'|trans }}</h6>
                                            <div class="dropdown-divider"></div>
                                            {% set path = term_path(term) %}
                                            {% if path %}
                                                <a class="dropdown-item dropdown-item-with-icon action-element-single-auto-form" href="{{ path }}" target="_blank" title="{{ 'previewTerm'|trans({}, taxonomyType.translationDomain) }}"><i class="dropdown-icon fas fa-eye"></i> {{ 'previewTerm'|trans({}, taxonomyType.translationDomain) }}</a>
                                            {% endif %}
                                            <a class="dropdown-item dropdown-item-with-icon dropdown-item-danger action-element-single" href="#" data-action="delete" title="{{ 'deleteTerm'|trans({}, taxonomyType.translationDomain) }}"><i class="dropdown-icon fas fa-times"></i> {{ 'deleteTerm'|trans({}, taxonomyType.translationDomain) }}</a>
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
                <a href="{{ path('backend.term.create', { taxonomyType: criteria.taxonomy_type }) }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
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
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            new Tulia.ElementsActions({
                actions: {
                    delete: {
                        headline: '{{ 'deleteSelectedNodes'|trans({}, taxonomyType.translationDomain) }}',
                        question: '{{ 'areYouSureYouWantToDeleteFollowingNodes'|trans({}, taxonomyType.translationDomain) }}',
                        action: '{{ path('backend.term.delete', { taxonomyType: taxonomyType.type, _token: csrf_token('term.delete') }) }}',
                    },
                }
            });

            let query = '{{ app.request.query.get('q') }}';

            if (query) {
                $('.term-name').each(function () {
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
    </script>
{% endblock %}
