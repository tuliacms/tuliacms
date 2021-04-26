{% extends 'backend' %}

{% block title %}
    {{ 'widgets'|trans({}, 'widgets') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'widgets'|trans({}, 'widgets') }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="#" class="btn btn-success btn-icon-left" data-toggle="modal" data-target="#modal-widget-create"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-window-restore"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.widget.datatable')
        }) }}
    </div>
    <div class="modal fade" id="modal-widget-create" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ 'availableWidgets'|trans({}, 'widgets') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {% for widget in availableWidgets %}
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card">
                                    <h5 class="card-header text-center">
                                        {{ widget.info.name|default('_name not provided_')|trans({}, widget.info.translation_domain|default('widgets')) }}
                                    </h5>
                                    <div class="card-body text-center">
                                        <p class="widget-description">{{ widget.info.description|default('')|trans({}, widget.info.translation_domain|default('widgets')) }}</p>
                                        <a href="{{ path('backend.widget.create', { id: widget.id, space: space }) }}" class="btn btn-primary btn-sm">{{ 'addWidget'|trans({}, 'widgets') }}</a>
                                    </div>
                                </div>
                            </div>
                        {% endfor %}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ 'cancel'|trans }}</button>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
