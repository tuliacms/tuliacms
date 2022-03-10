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
                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-menu-add" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.menu.datatable')
        }) }}
    </div>
    <div class="modal fade" id="modal-menu-add" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ path('backend.menu.create') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token('menu.create') }}" />
                    <div class="modal-header">
                        <h5 class="modal-title">{{ 'addMenu'|trans({}, 'menu') }}</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
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
                        <button type="button" class="close" data-bs-dismiss="modal">
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
                        <button type="submit" class="btn btn-success">{{ 'save'|trans }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('#modal-menu-add').on('shown.bs.modal', function () {
                $(this).find('input[name=name]').trigger('focus');
            });
            $('#modal-menu-edit').on('show.bs.modal', function (e) {
                $(this).find('input[name=id]').val($(e.relatedTarget).attr('data-element-id'));
                $(this).find('input[name=name]').val($(e.relatedTarget).attr('data-element-name'));
            }).on('shown.bs.modal', function () {
                $(this).find('input[name=name]').trigger('focus');
            });
        });
    </script>
{% endblock %}
