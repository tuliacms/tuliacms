{% extends 'backend' %}

{% block title %}
    {{ 'menuList'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'menuList'|trans }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="#" data-toggle="modal" data-target="#modal-menu-add" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'add'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>

        {{ generator.generate(datatable, {
            data_endpoint: path('backend.menu.datatable')
        }) }}

        {#{% if menus is not empty %}
            <table class="table pages-list">
                <thead>
                    <tr>
                        <th class="text-center col-uuid">ID</th>
                        <th class="text-left">{{ 'name'|trans }}</th>
                        <th class="col-actions">{{ 'actions'|trans }}</th>
                    </tr>
                </thead>
                <tbody>
                {% for menu in menus %}
                    <tr data-element-name="{{ menu.name }}" data-element-id="{{ menu.id }}">
                        <td data-label="ID" class="text-center col-uuid">
                            <span class="short-uuid-shower" data-copy-dblclick="{{ menu.id }}" data-toggle="tooltip" title="Kopiuj -> dwuklik">{{ menu.id|shorten_uuid }}</span>
                        </td>
                        <td data-label="{{ 'name'|trans }}" class="col-title">
                            <a href="{{ path('backend.menu.item.list', { menuId: menu.id }) }}" class="link-title">
                                {{ menu.name }}
                            </a>
                        </td>
                        <td data-label="{{ 'actions'|trans }}" class="col-actions">
                            <div class="actions-box">
                                <a href="#" class="btn btn-secondary btn-icon-only" data-toggle="tooltip" title="Szybka edycja"><i class="btn-icon fas fa-map"></i></a>
                                <div class="btn-group">
                                    <a href="#" data-toggle="modal" data-target="#modal-menu-edit" class="btn btn-secondary btn-icon-only btn-main-action">
                                        <i class="btn-icon fas fa-pen"></i>
                                    </a>
                                    <div class="btn-group" role="group">
                                        <button itype="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"></button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <h6 class="dropdown-header">{{ 'moreOptions'|trans }}</h6>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.menu.item.list', { menuId: menu.id }) }}" title="{{ 'menuItems'|trans({}, 'menu') }}"><i class="dropdown-icon fas fa-bars"></i> {{ 'menuItems'|trans({}, 'menu') }}</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item dropdown-item-with-icon dropdown-item-danger action-element-single" href="#" data-action="delete" title="{{ 'deleteMenu'|trans({}, 'menu') }}"><i class="dropdown-icon fas fa-times"></i> {{ 'deleteMenu'|trans({}, 'menu') }}</a>
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
                <a href="#" data-toggle="modal" data-target="#modal-menu-add" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'add'|trans }}</a>
            </div>
        {% endif %}
        <div class="pane-footer"></div>#}
    </div>
    <div class="modal fade" id="modal-menu-add" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ path('backend.menu.create') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token('menu.create') }}" />
                    <div class="modal-header">
                        <h5 class="modal-title">{{ 'addMenu'|trans({}, 'menu') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <fieldset class="form-group">
                            <label>{{ 'menuName'|trans({}, 'menu') }}</label>
                            <input type="text" class="form-control" name="name" />
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'close'|trans }}</button>
                        <button type="submit" class="btn btn-success">{{ 'save'|trans }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-menu-edit" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ path('backend.menu.edit') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token('menu.edit') }}" />
                    <div class="modal-header">
                        <h5 class="modal-title">{{ 'editMenu'|trans({}, 'menu') }}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="id" />
                        <fieldset class="form-group">
                            <label>{{ 'menuName'|trans({}, 'menu') }}</label>
                            <input type="text" class="form-control" name="name" />
                        </fieldset>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'close'|trans }}</button>
                        <button type="submit" class="btn btn-success">{{ 'save'|trans }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            new Tulia.ElementsActions({
                actions: {
                    delete: {
                        headline: '{{ 'deleteSelectedMenus'|trans({}, 'menu') }}',
                        question: '{{ 'areYouSureYouWantToDeleteFollowingMenus'|trans({}, 'menu') }}',
                        action: '{{ path('backend.menu.delete', { _token: csrf_token('menu.delete') }) }}',
                    },
                }
            });

            $('#modal-menu-add').on('shown.bs.modal', function () {
                $(this).find('input[name=name]').trigger('focus');
            });

            $('#modal-menu-edit').on('show.bs.modal', function (e) {
                let id = $(e.relatedTarget).attr('data-element-id');
                let name = $(e.relatedTarget).attr('data-element-name');

                $(this).find('input[name=id]').val(id);
                $(this).find('input[name=name]').val(name);
            }).on('shown.bs.modal', function (e) {
                $(this).find('input[name=name]').trigger('focus');
            });
        });
    </script>
{% endblock %}
